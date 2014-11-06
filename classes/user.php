<?php
//user class
class User
{
	private $user = array ();
	private $loggedIn = false;
	
	public function __construct ($idOrData)
	{
		if (is_int ($idOrData))
		{
			if ($idOrData == 0)
			{
				//get logged in user
				$this->getcookie ();
			}
			else
			{
				//call user data from database
				$this->setUserById($idOrData);
			}
		}
		else if (!empty ($idOrData ['email']))
		{
			//user data sent through. Start by checking if email address exists in DB
			$test = $this->setUserByEmail ($idOrData ['email']);
			if ($test == 0)
			{
				//user does not exist
				$this->user = $idOrData;
				$this->commitUser ();
			}
		}
	}
	
	public function setUserById ($userId)
	{
		$result = $GLOBALS ['conn']->query("SELECT * FROM ".db_prefix()."_login WHERE loginId = $userId");
		if ($result->num_rows > 0)
		{
			$this->user = $result->fetch_assoc();
			$this->add_user_details ();
		}
	}
	
	public function setUserByEmail ($email)
	{
		$result = $GLOBALS ['conn']->query("SELECT * FROM ".db_prefix()."_login WHERE email = '$email'");
		if ($result->num_rows > 0)
		{
			$this->user = $result->fetch_assoc();
			$this->add_user_details ();
			return 1;
		}
		else
		{
			return 0;
		}
	}
	
	public function getUser ()
	{
		return $this->user;
	}
	
	public function isLoggedIn ()
	{
		return $this->loggedIn;
	}
	
	public function getDetail ($detail)
	{
		$detail = strtolower ($detail);
		if (!empty ($this->user [$detail]))
			return $this->user [$detail];
		else
			return 0;
	}
	
	public function setDetail ($detail, $value)
	{
		$this->user [$detail] = $value;
		if (!empty ($this->user ['loginId']))
		{
			if (strcmp ($detail, "email") == 0)
			{
				//update login table
				$GLOBALS ['conn']->query ("UPDATE ".db_prefix()."_login SET email = '$value' WHERE loginId = ".$this->user ['loginId']);
			}
			else if (strcmp ($detail, "password") == 0)
			{
				//encrypt password and update login table
				$password = md5 ($detail);
				$GLOBALS ['conn']->query ("UPDATE ".db_prefix()."_login SET password = '$password' WHERE loginId = ".$this->user ['loginId']);
			}
			else
			{
				$result = $GLOBALS ['conn']->query ("SELECT value FROM ".db_prefix()."_user_details WHERE name LIKE '$detail' AND loginId = ".$this->user ['loginId']);
				if ($result->num_rows > 0)
				{
					//update
					$GLOBALS ['conn']->query ("UPDATE ".db_prefix()."_user_details SET value = '$value' WHERE loginId = ".$this->user ['loginId']." AND name = '$detail'");
				}
				else
				{
					//insert
					$GLOBALS ['conn']->query ("INSERT INTO ".db_prefix()."_user_details (loginId, name, value) VALUES (".$this->user ['loginId'].", '$detail', '$value')");
				}
			}
		}
	}
	
	private function getcookie ()
	{
		if (isset ($_COOKIE [db_prefix()."-login"]))
		{
			$code = $_COOKIE [db_prefix()."-login"];
			$result = $GLOBALS ['conn']->query("SELECT loginId FROM ".db_prefix()."_cookie_tracker WHERE cookie = '".$code."'");
			if ($row = $result->fetch_assoc())
			{
				$result2 = $GLOBALS ['conn']->query("SELECT * FROM ".db_prefix()."_login WHERE loginId = ".$row ['loginId']);
				$temp = $result2->fetch_assoc();
				foreach ($temp as $detail=>$value)
				{
					
					$detail = strtolower ($detail);
					$this->user [$detail] = $value;
				}
				$this->add_user_details ();
				$this->loggedIn = true;
			}
		}
	}
	
	private function add_user_details ()
	{
		$result = $GLOBALS ['conn']->query("SELECT name, value FROM ".db_prefix()."_user_details WHERE loginId = '".$this->user ['loginid']."'");
		while ($row = $result->fetch_assoc())
		{
			$this->user [strtolower ($row ['name'])] = $row ['value'];
		}
		$result = $GLOBALS ['conn']->query("SELECT name FROM ".db_prefix()."_user_type WHERE userType = ".$this->user ['usertype']);
		$row = $result->fetch_assoc ();
		$this->user ['usertypenName'] = $row ['name'];
	}
	
	private function commitUser ()
	{
		//run a few checks to avoid security vulnerabilities
		$this->user ['password'] = md5($_POST ['password']);
		$registration_date = date ("Y-m-d");
		$sql = "INSERT INTO ".db_prefix()."_login (email, userType, password) VALUES ('".$this->user ['email']."',".$this->user ['usertype'].",'".$this->user ['password']."')";
		$result = $GLOBALS ['conn']->query ($sql);
		$this->user ['loginId'] = $GLOBALS ['conn']->insert_id;
		$sql = "INSERT INTO ".db_prefix()."_user_details (loginId, name, value) VALUES (".$this->user ['loginid'].",'dateRegistered','$registration_date');\n";
		foreach ($this->user as $detail=>$value)
		{
			if (strcmp ($detail, "userType") == 0 || strcmp ($detail, "email") == 0 || strcmp ($detail, "password") == 0 || strcmp ($detail, "loginId") == 0)
			{
				//do nothing
			}
			else
			{
				$sql .= "INSERT INTO ".db_prefix()."_user_details (loginId, name, value) VALUES (".$this->user ['loginid'].",'$detail','$value');\n";
			}
		}
		$result = $GLOBALS ['conn']->multi_query ($sql);
	}
	
	public function deleteUser ()
	{
		$GLOBALS ['conn']->query ("DELETE FROM ".db_prefix()."_login WHERE loginId = ".$this->user ['loginid']);
		$GLOBALS ['conn']->query ("DELETE FROM ".db_prefix()."_user_details WHERE loginId = ".$this->user ['loginid']);
	}
}
?>