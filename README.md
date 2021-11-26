# SzkolnaBazaDanych
System dla Zespołu Szkół Technicznych w Pile, pozwalający uczniom na przechowywanie swoich stron internetowych oraz na dostęp do baz danych na potrzeby stron.
<br><br>
Konfiguracja:<br>
<br>
Połączenie z bazą danych<br>
./panel/modules/mysql.php<br>
&nbsp;&nbsp;$username - Nazwa konta mysql<br>
&nbsp;&nbsp;$password - Hasło<br>
<br>
Połączenie konta email<br>
./panel/modules/sendmail.php<br>
&nbsp;&nbsp;$host - <br>
&nbsp;&nbsp;$username - Adres email do wysyłania maili<br>
&nbsp;&nbsp;$password - Hasło<br>
<br>
<br>
Użytkownicy nie mają dostępu do ssh oraz baz danych innych użytkowników.<br>
Po zaakceptowaniu konta przez admina zostaje utowrzone konto w bazie danych, domyślna baza danych użytkownika oraz konto w systemie linux.<br>
Dodatkowo trzeba skonfigurować serwer FTP, aby umożliwić przesyłanie plików użytkownikom.<br>
<br>
<br>
Konto admina:<br><br>
admin<br>
admin
