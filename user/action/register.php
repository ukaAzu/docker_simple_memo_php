<?php
session_start();
require '../../common/validation.php';
require '../../common/database.php';

$user_name = $_POST['user_name'];
$user_email = $_POST['user_email'];
$user_password = $_POST['user_password'];

$_SESSION['errors'] = [];

emptyCheck($_SESSION['errors'], $user_name, "ユーザー名を入力してください");
emptyCheck($_SESSION['errors'], $user_email, "メールアドレスを入力してください");
emptyCheck($_SESSION['errors'], $user_password, "パスワードを入力してください");

stringMaxSizeCheck($_SESSION['errors'], $user_name, "ユーザー名は255文字以内で入力してください");
stringMaxSizeCheck($_SESSION['errors'], $user_email, "メールアドレスは255文字以内で入力してください");
stringMaxSizeCheck($_SESSION['errors'], $user_password, "パスワードは255文字以内で入力してください");
stringMinSizeCheck($_SESSION['errors'], $user_password, "パスワードは8文字以上で入力してください");

if(!$_SESSION['errors']){
    mailAddressCheck($_SESSION['errors'], $user_email, "正しいメールアドレスを入力してください");

    halfAlphanumericCheck($_SESSION['errors'], $user_name, "ユーザー名は半角英数字で入力してください");
    halfAlphanumericCheck($_SESSION['errors'], $user_password, "パスワードは半角英数字で入力してください");

    mailAddressDuplicationCheck($_SESSION['errors'], $user_email, "すでに登録されているメールアドレスです");
}

if($_SESSION['errors']){
    header('Location: ../../user/');
    exit;
}

$database_handler = getDatabaseConnection();


try{
    if($statement = $database_handler->prepare('INSERT INTO users(name,email,password) VALUES (:name,:email,:password)')){
        $password = password_hash($user_password,PASSWORD_DEFAULT);

        $statement->bindParam(':name',htmlspecialchars($user_name));
        $statement->bindParam(':email',$user_email);
        $statement->bindParam(':password',$password);
        $statement->execute();
    }
}catch(Throwable $e){
    echo $e->getMessage();
    exit;
}

header('Location: ../../memo/');
exit;
