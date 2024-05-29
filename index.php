<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();
if(strpos($_SERVER['REQUEST_URI'], 'index.php') === false){
  header('Location: index.php');
  exit();
}

$log = !empty($_SESSION['login']);
$adminLog = !empty($_SERVER['PHP_AUTH_USER']);
$uid = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';
$getUid = isset($_GET['uid']) ? strip_tags($_GET['uid']) : '';

if($adminLog){
  if(preg_match('/^[0-9]+$/', $getUid)){
    $uid = $getUid;
    $log = true;
  }
}

$db;

include('database.php');
  
function isp($value){
  if(isset($value)) return $value;
  return;
}

function del_cook($cook, $vals = 0){
  setcookie($cook.'_error', '', 100000);
  if($vals) setcookie($cook.'_value', '', 100000);
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

  if(($adminLog && !empty($getUid)) || !$adminLog){
    $cookAdmin = (!empty($_COOKIE['admin_value']) ? $_COOKIE['admin_value'] : '');
    if($cookAdmin == '1'){
      del_cook('name', 1);
      del_cook('number', 1);
      del_cook('email', 1);
      del_cook('data', 1);
      del_cook('radio', 1);
      del_cook('lang', 1);
      del_cook('biography', 1);
      del_cook('check_mark', 1);
      del_cook('admin', 1);
    }
  }


  $name = (!empty($_COOKIE['name_error']) ? $_COOKIE['name_error'] : '');
  $number = (!empty($_COOKIE['number_error']) ? $_COOKIE['number_error'] : '');
  $email = (!empty($_COOKIE['email_error']) ? $_COOKIE['email_error'] : '');
  $data = (!empty($_COOKIE['data_error']) ? strtotime($_COOKIE['data_error']) : '');
  $radio = (!empty($_COOKIE['radio_error']) ? $_COOKIE['radio_error'] : '');
  $lang = (!empty($_COOKIE['lang_error']) ? $_COOKIE['lang_error'] : '');
  $biography = (!empty($_COOKIE['biography_error']) ? $_COOKIE['biography_error'] : '');
  $check_mark = (!empty($_COOKIE['check_mark_error']) ? $_COOKIE['_error'] : '');

  $errors = array();
  $messages = array();
  $values = array();
  $error = true;

function setVal($enName, $param){
  global $values;
  $values[$enName] = empty($param) ? '' : strip_tags($param);
}

  function val_empty($enName, $val){
    global $error, $errors, $values, $messages;
    if($error) 
    $error = empty($_COOKIE[$enName.'_error']);
    $errors[$enName] = !empty($_COOKIE[$enName.'_error']);
    $messages[$enName] = "<div class='messageError'>$val</div>";
    $values[$enName] = empty($_COOKIE[$enName.'_value']) ? '' : $_COOKIE[$enName.'_value'];
    del_cook($enName);
    return;
  }



  if (!empty($_COOKIE['save'])) {
    setcookie('save', '', 100000);
    setcookie('login', '', 100000);
    setcookie('password', '', 100000);
    $messages['success'] = 'Данные сохранены';
    if (!empty($_COOKIE['password'])) {
      $messages['info'] = sprintf('Вы можете <a href="login.php">войти</a> с логином <strong>%s</strong>
        и паролем <strong>%s</strong> для изменения данных.',
        strip_tags($_COOKIE['login']),
        strip_tags($_COOKIE['password']));
    }
  }

  val_empty('name', $name);
  val_empty('number', $number);
  val_empty('email', $email);
  val_empty('data', $data);
  val_empty('radio', $radio);
  val_empty('lang', $lang);
  val_empty('biography', $biography);
  val_empty('check_mark',$check_mark);

  $langsa = explode(',', $values['lang']);


  if ($error && $log ) {
    try {
      $dbFD = $db->prepare("SELECT * FROM form_data WHERE user_id = ?");
      $dbFD->execute([$_SESSION['user_id']]);
      $fet = $dbFD->fetchAll(PDO::FETCH_ASSOC)[0];
      $form_id = $fet['id'];
      $_SESSION['form_id'] = $form_id;
      $dbL = $db->prepare("SELECT l.name FROM form_data_lang fdl
                            JOIN languages l ON l.id = fdl.id_lang
                            WHERE id_form = ?");
      $dbL->execute([$form_id]);
      $langsa = [];
      foreach($dbL->fetchAll(PDO::FETCH_ASSOC) as $item){
        $langsa[] = $item['name'];
      }
      setVal('name', $fet['name']);
      setVal('number', $fet['number']);
      setVal('email', $fet['email']);
      setVal('data', date("Y-m-d", $fet['data']));
      setVal('radio', $fet['radio']);
      setVal('lang', $lang);
      setVal('biography', $fet['biography']);
      setVal('check_mark', '1');
    }
    catch(PDOException $e){
      print('Error : ' . $e->getMessage());
      exit();
    }
  }
  include('form.php');
}
else{ 
  $name = (!empty($_POST['name']) ? $_POST['name'] : '');
  $number = (!empty($_POST['number']) ? $_POST['number'] : '');
  $email = (!empty($_POST['email']) ? $_POST['email'] : '');
  $data = (!empty($_POST['data']) ? ($_POST['data']) : '');
  $radio = (!empty($_POST['radio']) ? $_POST['radio'] : '');
  $lang = (!empty($_POST['lang']) ? $_POST['lang'] : '');
  $biography = (!empty($_POST['biography']) ? $_POST['biography'] : '');
  $check_mark = (!empty($_POST['check_mark']) ? $_POST['check_mark'] : '');
  $error = false;

  if(isset($_POST['logout_form'])){
    if($adminLog && empty($_SESSION['login'])){
      header('Location: admin.php');
    }
    else{
    del_cook('name', 1);
    del_cook('number', 1);
    del_cook('email', 1);
    del_cook('data', 1);
    del_cook('radio', 1);
    del_cook('lang', 1);
    del_cook('biography', 1);
    del_cook('radio', 1);
    session_destroy();
    header('Location: index.php'.(($getUid != NULL) ? '?uid='.$uid : ''));
    }
    exit();
  } 

  $number1 = preg_replace('/\D/', '', $number);

  function val_empty($cook, $comment, $usl){
    global $error;
    $res = false;
    $setVal = $_POST[$cook];
    if ($usl) {
      setcookie($cook.'_error', $comment, time() + 24 * 60 * 60); //сохраняем на сутки
      $error = true;
      $res = true;
    }
    
    if($cook == 'lang'){
      global $lang;
      $setVal = ($lang != '') ? implode(",", $lang) : '';
    }
    
    setcookie($cook.'_value', $setVal, time() + 30 * 24 * 60 * 60); //сохраняем на месяц
    return $res;
  }
  
  if(!val_empty('name', 'Заполните поле', empty($name))){
    if(!val_empty('name', 'Длина поля > 255 символов', strlen($name) > 255)){
      val_empty('name', 'Поле не соответствует требованиям: <i>Фаимлмя Имя (Отчество)</i>, кириллицей', !preg_match('/^([а-яё]+-?[а-яё]+)( [а-яё]+-?[а-яё]+){1,2}$/Diu', $name));
    }
  }
  if(!val_empty('number', 'Заполните поле', empty($number))){
    if(!val_empty('number', 'Длина поля некорректна', strlen($number) != 11)){
      val_empty('number', 'Поле должен содержать только цифры"', ($number != $number1));
    }
  }
  if(!val_empty('email', 'Заполните поле', empty($email))){
    if(!val_empty('email', 'Длина поля > 255 символов', strlen($email) > 255)){
      val_empty('email', 'Поле не соответствует требованию example@mail.ru', !preg_match('/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/', $email));
    }
  }
  if(!val_empty('data', "Выберите дату рождения", empty($data))){
    val_empty('data', "Неверно введена дата рождения, дата больше настоящей", (strtotime('now') < strtotime($data)));
  }
  val_empty('radio', "Выберите пол", (empty($radio) || !preg_match('/^(m|f)$/', $radio)));
  if(!val_empty('lang', "Выберите хотя бы один язык", empty($lang))){
   
    try {
      $inQuery = implode(',', array_fill(0, count($lang), '?'));
      $dbLangs = $db->prepare("SELECT id, name FROM languages WHERE name IN ($inQuery)");
      foreach ($lang as $key => $value) {
        $dbLangs->bindValue(($key+1), $value);
      }
      $dbLangs->execute();
      $languages = $dbLangs->fetchAll(PDO::FETCH_ASSOC);
    }
    catch(PDOException $e){
      print('Error : ' . $e->getMessage());
      exit();
    }
    
    val_empty('lang', 'Неверно выбраны языки', $dbLangs->rowCount() != count($lang));
  }
  if(!val_empty('biography', 'Заполните поле', empty($biography))){
    val_empty('biography', 'Длина текста > 65 535 символов', strlen($biography) > 65535);
  }
  val_empty('check_mark', "Ознакомьтесь с контрактом", empty($check_mark));
  
  if ($error) {
    // При наличии ошибок перезагружаем страницу и завершаем работу скрипта.
    header('Location: index.php');
    exit();
  }
  else {
    del_cook('name');
    del_cook('number');
    del_cook('email');
    del_cook('data');
    del_cook('radio');
    del_cook('lang');
    del_cook('biography');
    del_cook('check_mark');
  }
  


   // Проверяем меняются ли ранее сохраненные данные или отправляются новые.
   if ($log) {
      
    $stmt = $db->prepare("UPDATE form_data SET name = ?, number = ?, email = ?, data = ?, radio = ?, biography = ? WHERE user_id = ?");
    $stmt->execute([$name, $number, $email, strtotime($data), $radio, $biography, $_SESSION['user_id']]);

    $stmt = $db->prepare("DELETE FROM form_data_lang WHERE id_form = ?");
    $stmt->execute([$_SESSION['form_id']]);

    $stmt1 = $db->prepare("INSERT INTO form_data_lang (id_form, id_lang) VALUES (?, ?)");
    foreach($languages as $row){
        $stmt1->execute([$_SESSION['form_id'], $row['id']]);
    }
    // TODO: перезаписать данные в БД новыми данными,
    // кроме логина и пароля.

    if($adminLog) 
        setcookie('admin_value', '1', time() + 30 * 24 * 60 * 60);
      // TODO: перезаписать данные в БД новыми данными,
      // кроме логина и пароля.

  }
  else {
    // Генерируем уникальный логин и пароль.
    // TODO: сделать механизм генерации, например функциями rand(), uniquid(), md5(), substr().
    $login = substr(uniqid(), 0, 4).rand(10, 100);
    $password = rand(100, 1000).substr(uniqid(), 4, 10);
    // Сохраняем в Cookies.
    setcookie('login', $login);
    setcookie('password', $password);

    $mpassword = md5($password);


  try {

    $stmt=$db->prepare("INSERT INTO users(login, password) VALUES (?, ?)");
    $stmt->execute([$login, md5($password) ]);
    $user_id = $db->lastInsertId();

    $fid = $db->lastInsertId();
    $stmt = $db->prepare("INSERT INTO form_data (user_id, name, number, email, data, radio, biography) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$fid, $name, $number, $email, strtotime($data), $radio, $biography]);
    $stmt1 = $db->prepare("INSERT INTO form_data_lang (id_form, id_lang) VALUES (?, ?)");
    foreach($languages as $row){
        $stmt1->execute([$fid, $row['id']]);
    }
  }
  catch(PDOException $e){
    print('Error : ' . $e->getMessage());
    exit();
  }
  setcookie('name_value', $name, time() + 24 * 60 * 60 * 365);
  setcookie('number_value', $number, time() + 24 * 60 * 60 * 365);
  setcookie('email_value', $email, time() + 24 * 60 * 60 * 365);
  setcookie('data_value', $data, time() + 24 * 60 * 60 * 365);
  setcookie('radio_value', $radio, time() + 24 * 60 * 60 * 365);
  setcookie('lang_value', implode(",", $lang), time() + 24 * 60 * 60 * 365);
  setcookie('biography_value', $biography, time() + 24 * 60 * 60 * 365);
  setcookie('check_mark_value', $check_mark, time() + 24 * 60 * 60 * 365);
}
  // Сохраняем куку с признаком успешного сохранения.
  setcookie('save', '1');

  // Делаем перенаправление.
  header('Location: index.php');

}
?>