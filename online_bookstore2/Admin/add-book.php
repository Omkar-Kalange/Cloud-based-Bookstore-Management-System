<?php
    session_start();

    require('connect.php');
    require('s3-config.php');

    if(empty($_SESSION['adminID']))
    {
        header("Location: login.php"); 
    }
        
    $adminID = $_SESSION["adminID"];

    $sql = "select * from admins where adminID ='$adminID'"; 
    $result = $conn->query($sql);
    $row2 = mysqli_fetch_array($result);
    $name = $row2['firstName'].' '.$row2['lastName'];

    $profPic = '0.png'; 
    if($row2['profilePic']=='Y')
    {
        $profPic = $adminID.'.png';
    }

    $cmd = $s3->getCommand('GetObject', [
        'Bucket' => 'onlinebookstoreservice',
        'Key'    => 'ProfilePics/Admin/'.$profPic
    ]);
    
    //The period of availability
    $request = $s3->createPresignedRequest($cmd, '+1 minutes');
    
    //Get the pre-signed URL
    $signedUrl = (string) $request->getUri();
    
    if(isset($_POST["btnAdd"]))
    {

        $title = $_POST['title'];
        $edition = $_POST['edition'];
        $author = $_POST['author'];
        $publisher = $_POST['publisher'];
        $genre = $_POST['genre'];
        $year = $_POST['year'];
        $bookPrice = $_POST['bookPrice'];
        $quantity = $_POST['quantity'];
        $ebook = $_POST['ebook'];
        $ebookPrice = $_POST['ebookPrice'];
        $synopsis = $_POST['synopsis'];

        if($ebook == 'Y')
            $query = "INSERT INTO `books` (`title`, `edition`, `author`, `publisher`, `genre`, `publicationYear`, `book_price`, `quantity`, `ebook_available`,`ebook_price`, `synopsis`) VALUES ('$title','$edition','$author','$publisher','$genre','$year','$bookPrice','$quantity','$ebook','$ebookPrice','$synopsis');";
        else
            $query = "INSERT INTO `books` (`title`, `edition`, `author`, `publisher`, `genre`, `publicationYear`, `book_price`, `quantity`, `synopsis`) VALUES ('$title','$edition','$author','$publisher','$genre','$year','$bookPrice','$quantity','$synopsis');";
        
        if($ebook == 'Y')
        {
            if($_FILES['pdf']['tmp_name']=='')
            {
                echo "<script> alert('Upload ebook');</script>";
            }
            else
            {
                try
                {
                    $result = mysqli_query($conn,$query);
                }
                catch(Exception $e)
                {
                    echo "<script> alert('Book already exists');</script>";
                }
                try
                {
                    $sql = "SELECT bookID FROM books WHERE title='".$title."' and edition=".$edition;
                    $result = $conn->query($sql);
                    $row = mysqli_fetch_array($result);
        
                    move_uploaded_file($_FILES["img"]["tmp_name"],$_SERVER["DOCUMENT_ROOT"]."/i.png");
                    $s3->putObject([
                        'Bucket'=>'onlinebookstoreservice',              //Bucket where to put image
                        'Key' => 'BookCover/'.$row['bookID'].'.png',     //Name of the file in the bucket
                        'Body' =>fopen($_SERVER["DOCUMENT_ROOT"]."/i.png",'rb')                     //File can be viewed on S3
                    ]);
                    unlink($_SERVER["DOCUMENT_ROOT"]."/i.png");
                }
                catch(Exception $e)
                {
                    echo "<script> alert('Problem in adding book');</script>";
                }
                try
                {
                    $sql = "SELECT bookID FROM books WHERE title='".$title."' and edition=".$edition;
                    $result = $conn->query($sql);
                    $row = mysqli_fetch_array($result);
        
                    move_uploaded_file($_FILES["pdf"]["tmp_name"],$_SERVER["DOCUMENT_ROOT"]."/i.pdf");
                    $s3->putObject([
                        'Bucket'=>'onlinebookstoreservice',              //Bucket where to put image
                        'Key' => 'Ebooks/'.$row['bookID'].'.pdf',     //Name of the file in the bucket
                        'Body' =>fopen($_SERVER["DOCUMENT_ROOT"]."/i.pdf",'rb')                     //File can be viewed on S3
                    ]);
                    echo "<script> alert('Book Added successfully');</script>";
                    unlink($_SERVER["DOCUMENT_ROOT"]."/i.png");
                }
                catch(Exception $e)
                {
                    echo "<script> alert('Problem in adding book');</script>";
                }               
            }
        }
        else
        {
            try
            {
                $result = mysqli_query($conn,$query);
            }
            catch(Exception $e)
            {
                echo "<script> alert('Book already exists');</script>";
            }
            try
            {
                $sql = "SELECT bookID FROM books WHERE title='".$title."' and edition=".$edition;
                $result = $conn->query($sql);
                $row = mysqli_fetch_array($result);
    
                move_uploaded_file($_FILES["img"]["tmp_name"],$_SERVER["DOCUMENT_ROOT"]."/i.png");
                $s3->putObject([
                    'Bucket'=>'onlinebookstoreservice',              //Bucket where to put image
                    'Key' => 'BookCover/'.$row['bookID'].'.png',     //Name of the file in the bucket
                    'Body' =>fopen($_SERVER["DOCUMENT_ROOT"]."/i.png",'rb')                     //File can be viewed on S3
                ]);
                unlink($_SERVER["DOCUMENT_ROOT"]."/i.png");
            
                echo "<script> alert('Book Added successfully');</script>";
            }
            catch(Exception $e)
            {
                echo "<script> alert('Problem in adding book');</script>";
            }  
        }
    }  
?>

<!DOCTYPE html>
    <head>
        <title>Add book</title>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Sofia">
        <link rel="stylesheet" href="Style.css?v=<?php echo time(); ?>">
    </head>
    <body>
        <table>
            <tr id="top-margin">
                <td id="web-name">
                    <h2>The Big Bookshelf.com</h2>
                </td>
                <td>
                    <h3><a class="username" href="show_profile.php"><?php echo $name;  ?></a></h3>
                </td>
                <td id="userImg">
                    <img src=<?php echo $signedUrl; ?> alt="User Image" width="50" height="50"></img>
                </td>
            </tr>
        </table>
        <table>
            <tr>
                <td class="sidebar">
                    <?php
                    include('adminOptions.php');
                    ?>
                </td>
                <td>
                    <div class="main">
                        <h1 style='text-align:center;'><i><u>Add New Book</u></i></h1>
                        <form id="form" method="post" enctype="multipart/form-data">
                            <label>Add book image</label>
                            <input type="file" name='img' required>
                            <br><br>
                            <label>Title</label>
                            <input name="title" placeholder="Enter Title" required></input>
                            <br><br>
                            <label>Edition</label>
                            <input name="edition" value=1 required></input>
                            <br><br>
                            <label>Author</label>
                            <input name="author" placeholder="Enter Author(s)" required></input>
                            <br><br>
                            <label>Publisher</label>
                            <input name="publisher" placeholder="Enter Publisher" required></input>
                            <br><br>
                            <label>Genre</label>
                            <input name="genre" placeholder="Enter Genre" required></input>
                            <br><br>
                            <label>Year</label>
                            <input name="year" placeholder="Enter year of publication" required></input>
                            <br><br>
                            <label>Price of Book</label>
                            <input type='number' name="bookPrice" placeholder="Enter Price in Rs." required></input>
                            <br><br>
                            <label>Quantity</label>
                            <input type='number' name="quantity" value=5 required></input>
                            <br><br>
                            <label>Is ebook available?</label>
                            <select name="ebook">
                                <option value="N">No</option>
                                <option value="Y">Yes</option>
                            </select>
                            <br><br>
                            <label>Upload ebook (pdf)</label>
                            <input type="file" name='pdf'>
                            <br><br>
                            <label>Price of ebook</label>
                            <input type='number' name="ebookPrice" placeholder="Enter Price in Rs."></input>
                            <br><br>
                            <label>Synopsis</label>
                            <br>
                            <textarea name="synopsis" placeholder='Enter book description...' required></textarea>
                            <br><br>
                            <input type="submit" value="Add Books" name="btnAdd"></input>
                        </form>
                    </div>
                </td>
            </tr>
        </table>
    </body>
</html>


