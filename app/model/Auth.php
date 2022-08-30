<?php
namespace Acd\Model;
use Acd\conf;

class AuthInvalidUserException extends \exception {}
class Auth  {
	public static function hashPassword($password, $cost=11){
		//return password_hash($password, PASSWORD_DEFAULT); // php5.5

		/* To generate the salt, first generate enough random bytes. Because
		 * base64 returns one character for each 6 bits, the we should generate
		 * at least 22*6/8=16.5 bytes, so we generate 17. Then we get the first
		 * 22 base64 characters
		 */
		$salt=substr(base64_encode(openssl_random_pseudo_bytes(17)),0,22);
		/* As blowfish takes a salt with the alphabet ./A-Za-z0-9 we have to
		 * replace any '+' in the base64 string with '.'. We don't have to do
		 * anything about the '=', as this only occurs when the b64 string is
		 * padded, which is always after the first 22 characters.
		 */
		$salt=str_replace("+",".",$salt);
		/* Next, create a string that will be passed to crypt, containing all
		 * of the settings, separated by dollar signs
		 */
		$param='$'.implode('$',array(
			"2y", //select the most secure version of blowfish (>=PHP 5.3.7)
			str_pad($cost,2,"0",STR_PAD_LEFT), //add the cost in two digits
			$salt //add the salt
		));

		//now do the actual hashing
		return crypt($password,$param);
	}
	/*
	* Check the password against a hash generated by the generate_hash
	* function.
	*/
	public static function validate_pw($password, $hash){
		// password_verify($password, $hash); // php5.5

		/* Regenerating the with an available hash as the options parameter should
		 * produce the same hash if the same password is passed.
		 */
		return crypt($password, $hash)==$hash;
	}

	// TODO QUITAR
	private static function persistentFilePath($login) {
		return conf::$PATH_AUTH_PERMANENT_LOGIN_DIR.'/'.hash('sha1', $login);
	}
	public static function isLoged() {
		if (conf::$USE_AUTHENTICATION === false) {
			Auth::loginByFake();
			return true;
		}
		elseif  (isset($_SESSION['loged']) && $_SESSION['loged'] === true) {
			return true;
		}
		else {
			$loginCookie = isset($_COOKIE[conf::$COOKIE_PREFIX.'login']) ? $_COOKIE[conf::$COOKIE_PREFIX.'login'] : null;
			$token = isset($_COOKIE[conf::$COOKIE_PREFIX.'token']) ? $_COOKIE[conf::$COOKIE_PREFIX.'token'] : null;

			return Auth::loginByPersintence($loginCookie, $token);
		}
	}

	public static function getRol() {
		return $_SESSION['rol'];
	}

	// TODO: Borrar
	protected static function loadAllCredentials() {
		$path = conf::$PATH_AUTH_CREDENTIALS_FILE;
		$content = file_get_contents($path);
		$aCredentials = json_decode($content, true);

		return $aCredentials;
	}
	public static function loginByCredentials($login, $password, $remember) {
		if (!$login) throw new AuthInvalidUserException("Invalid login cannot be null value");
		$query = new Query();
		$query->setType('id');
		$query->setCondition(['id' => $login]);
		$userLoader = new UserLoader();
		$user = $userLoader->load($query);

		$bLoginCorrect = $user && Auth::validate_pw($password, $user->getPassword());

		// TODO: controlar errores
		// Remember login
		if ($bLoginCorrect && $remember) {
			$token = $userLoader->persistSession($user);
			$expiration = time()+conf::$AUTH_PERSITENT_EXPIRATION_TIME;
			setcookie(conf::$COOKIE_PREFIX.'login', $user->getId(), $expiration , '/', '', 0, 0);
			setcookie(conf::$COOKIE_PREFIX.'token', $token, $expiration, '/', '', 0, 1);
		}

		if ($bLoginCorrect) {
			$_SESSION['loged'] = true;
			$_SESSION['login_method'] = 'password';
			$_SESSION['login'] = $login;
			$_SESSION['rol'] = $user->getRol();
		}
		return $bLoginCorrect;
	}
	public static function loginByPersintence($login, $token) {
		$bLoginCorrect = false;
		$userLoader = new UserLoader();
		$user = $userLoader->loadPersistSession($token);
		if($user->getId() === $login && $login) {
			$_SESSION['loged'] = true;
			$_SESSION['login_method'] = 'persistence';
			$_SESSION['login'] = $login;
			$_SESSION['rol'] = $user->getRol();
			$bLoginCorrect = true;
		}

		return $bLoginCorrect;
	}
	// Mode no login required
	public static function loginByFake() {
			$_SESSION['loged'] = true;
			$_SESSION['login_method'] = 'fake';
			$_SESSION['login'] = 'no-login';
			$_SESSION['rol'] = conf::$ROL_DEVELOPER ;
	}
	public static function logout() {
		// Inicializar la sesión.
		// Si está usando session_name("algo"), ¡no lo olvide ahora!
		// The session start and destroy is executed in main page
		//session_start();

		// Destruir todas las variables de sesión.
		$_SESSION = array();

		// Si se desea destruir la sesión completamente, borre también la cookie de sesión.
		// Nota: ¡Esto destruirá la sesión, y no la información de la sesión!
		if (ini_get("session.use_cookies")) {
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000,
				$params["path"], $params["domain"],
				$params["secure"], $params["httponly"]
			);
		}

		// Finalmente, destruir la sesión.
		//session_destroy();

		// Eliminar los datos persistentes
		$userLoader = new UserLoader();
		$loginCookie = isset($_COOKIE[conf::$COOKIE_PREFIX.'login']) ? $_COOKIE[conf::$COOKIE_PREFIX.'login'] : null;
		$token = isset($_COOKIE[conf::$COOKIE_PREFIX.'token']) ? $_COOKIE[conf::$COOKIE_PREFIX.'token'] : null;
		if($token !== '') {
			$userLoader->deletePersistSession($token);
		}
		setcookie(conf::$COOKIE_PREFIX.'login', '', time() - 42000, '/', '', 0, 0);
		setcookie(conf::$COOKIE_PREFIX.'token', '', time() - 42000, '/', '', 0, 0);
	}
	public static function getCredentials($login) {
		$aCredentials = Auth::loadAllCredentials();
		// TODO: controlar errores
		if (isset($aCredentials[$login])) {
			$aCredentials = array(
				'loged' => true,
				'login_method' => 'only_login',
				'login' => $login,
				'rol' => $aCredentials[$login]['rol']);
		}
		else {
			$aCredentials = array(
				'loged' => false,
				'login_method' => 'only_login',
				'login' => $login,
				'rol' => '');
		}
		return $aCredentials;
	}
	public static function addUser($login, $password, $rol) {
		if ($login === '' || $password === '') {
			throw new AuthInvalidUserException("Invalid login or password [$login] : [$password]");
		}
		if ($rol !== conf::$ROL_DEVELOPER && $rol !== conf::$ROL_EDITOR) {
			throw new AuthInvalidUserException("Invalid rol [$rol]");
		}
		$aCredentials = Auth::loadAllCredentials();
		$aCredentials[$login]['password'] = Auth::hashPassword($password);
		$aCredentials[$login]['rol'] = $rol;
		$jsonCredentials = json_encode($aCredentials);

		$path = conf::$PATH_AUTH_CREDENTIALS_FILE;
		$tempPath = conf::$PATH_AUTH_CREDENTIALS_FILE.'.tmp';
		if (!$handle = fopen($tempPath, 'a')) {
			 echo "Cannot open file ($tempPath)";
			 exit;
		}

		// Write $jsonCredentials to our opened file.
		if (fwrite($handle, $jsonCredentials) === FALSE) {
			echo "Cannot write to file ($tempPath)";
			exit;
		}
		fclose($handle);
		rename($tempPath, $path);
	}
}
