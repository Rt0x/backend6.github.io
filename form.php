<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="index6.css" rel="stylesheet" type="text/css" />
    <title>Задание №5</title>
  </head>
  <body class="m-4">
      
      <form class="popup" action="" method="post">
      <div>
        <div class="message"><?php if(isset($messages['success'])) echo $messages['success']; ?></div>
        <div class="message message_info"><?php if(isset($messages['info'])) echo $messages['info']; ?></div>
        <div>
          <label>
            <input class="input <?php echo (isp($errors['name']) != NULL) ? 'borred' : ''; ?>" value="<?php echo isp($values['name']); ?>" type="text" name="name" placeholder="Ф.И.О">
            <div class="errpodinp"><?php echo $messages['name']?></div>
          </label>
        </div>

        <div>
          <label>
            <input class="input <?php echo (isp($errors['number']) != NULL) ? 'borred' : ''; ?>" value="<?php echo isp($values['number']); ?>" type="tel" name="number" placeholder="Номер телефона">
            <div class="errpodinp"><?php echo $messages['number']?></div>
          </label>
        </div>

        <div>
          <label>
            <input class="input <?php echo (isp($errors['email']) != NULL) ? 'borred' : ''; ?>" value="<?php echo isp($values['email']); ?>" type="email" name="email" placeholder="Введите почту">
            <div class="errpodinp"><?php echo $messages['email']?></div>

          </label>
        </div>

        <div>
          <label>
            <input class="input <?php echo (isp($errors['data']) != NULL) ? 'borred' : ''; ?>" value="<?php echo isp($values['data']); ?>" type="date" name="data">
            <div class="errpodinp"><?php echo $messages['data']?></div>
          </label>
        </div>

        <div class="my-3">
          Пол
          <br />
          <div class>
          <label>
                <input type="radio" name="radio" value="m" <?php if($values['radio'] == 'm') echo 'checked'; ?>>
                <span class=" <?php echo ($errors['radio'] != NULL) ? 'colred' : ''; ?>">Мужской</span>
            </label>
            <br>
            <label>
                <input type="radio" name="radio" value="f" ckeked <?php if($values['radio'] == 'f') echo 'checked'; ?>>
                <span class="<?php echo ($errors['radio'] != NULL) ? 'colred' : ''; ?>">Женский</span>
            </label>
            <div class="errpodinp"><?php echo $messages['radio']?></div>
          </div>
        </div>

        <div>
          <label class="input">
            Любимый язык программирования<br />
            <select  id="lang" class="my-2 <?php echo (isp($errors['lang']) != NULL) ? 'borred' : ''; ?>"  name="lang[]" multiple="multiple">
              <option value="Pascal" <?php echo (in_array('Pascal', $langsa)) ? 'selected' : ''; ?>>Pascal</option>
              <option value="C" <?php echo (in_array('C', $langsa)) ? 'selected' : ''; ?>>C</option>
              <option value="C++" <?php echo (in_array('C++', $langsa)) ? 'selected' : ''; ?>>C++</option>
              <option value="JavaScript" <?php echo (in_array('JavaScript', $langsa)) ? 'selected' : ''; ?>>JavaScript</option>
              <option value="PHP" <?php echo (in_array('PHP', $langsa)) ? 'selected' : ''; ?>>PHP</option>
              <option value="Python" <?php echo (in_array('Python', $langsa)) ? 'selected' : ''; ?>>Python</option>
              <option value="Java" <?php echo (in_array('Java', $langsa)) ? 'selected' : ''; ?>>Java</option>
              <option value="Haskel" <?php echo (in_array('Haskel', $langsa)) ? 'selected' : ''; ?>>Haskel</option>
              <option value="Clojure" <?php echo (in_array('Clojure', $langsa)) ? 'selected' : ''; ?>>Clojure</option>
              <option value="Scala" <?php echo (in_array('Scala', $langsa)) ? 'selected' : ''; ?>>Scala</option>
            </select>
            <div class="errpodinp"><?php echo $messages['lang']?></div>
          </label>
        </div>
        <br />
        <div>
          Биография <br />
          <label>
            <textarea name="biography" placeholder="Биография" class="input <?php echo (isp($errors['biography']) != NULL) ? 'borred' : ''; ?>"><?php echo isp($values['biography']); ?></textarea>
            <div class="errpodinp"><?php echo $messages['biography']?></div>
          </label>
        </div>

      
        <div >
            <input type="checkbox" name="check_mark"  <?php echo ( isp($values['check_mark']) != NULL) ? 'checked' : ''; ?>>
            <label for="check_mark" class="<?php echo (isp($errors['check_mark']) != NULL) ? 'colred' : ''; ?>">С контрактом ознакомлен (а)</label>
            <div class="errpodinp"><?php echo $messages['check_mark']?></div>
        </div>

        <?php
            if($log) echo '<button type="submit" class="form_button">Изменить</button>';
            else echo '<button type="submit" class="form_button">Отправить</button>';
        ?>

      <div class="mt-3">
      <?php 
            if($log) echo '<button type="submit" class="logout_form" name="logout_form">Выйти</button>'; 
            else echo '<a href="login.php" class="form_button" name="logout_form">Войти</a>';
        ?>
      </div>

      </div>
    </form>
</body>
</html>