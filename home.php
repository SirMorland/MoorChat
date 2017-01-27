<!DOCTYPE html>
<?php
$post = htmlspecialchars($_POST["nick"]);
if($post != "")
{
	$nick = $post;
	setcookie("nick", $nick, time() + (86400 * 30), "/");
}
else if($_COOKIE["nick"] == "")
{
	header("Location: http://moorchat.azurewebsites.net/");
	die();
}

$server = getenv("SQLCONNSTR_server");
$user = getenv("SQLCONNSTR_user");
$pwd = getenv("SQLCONNSTR_pwd");

try
{
	$conn = new PDO("sqlsrv:server = tcp:$server,1433; Database = moorchat_log",
		$user, $pwd);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e)
{
	
}

$newChannelName = htmlspecialchars($_POST["channelName"]);
if($newChannelName != "")
{
	$sql = "INSERT INTO Channels (C_Name)
			VALUES ('$newChannelName')";
	$conn->query($sql);
}

?>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name=viewport content="width=device-width, initial-scale=1">
		<meta name="theme-color" content="#a00" />
		<title>MoorChat</title>
		<link rel="stylesheet" href="style.css">
		<?php
			if($post == "")
			{
				if(isset($_COOKIE["nick"]))
				{
					$nick = $_COOKIE["nick"];
				}
			}
		?>
	</head>
	<body background="bg.png">
		<div class="column-left">
			<h1>Welcome <?php echo $nick ?>!</h1>
			<form action="home.php" method="post">
				<input type="text" name="nick" autocomplete="off" id="channels">
			</form>
			<ul>
				<?php
					class TableRows extends RecursiveIteratorIterator { 
					    function __construct($it) { 
					        parent::__construct($it, self::LEAVES_ONLY); 
					    }
					}
					$query = $conn->prepare('SELECT * FROM Channels');
					$query->execute();
					$result = $query->fetchAll();
					
					for($i = 0; $i < sizeof($result); $i++)
					{
						$c_name = $result[$i][1];
						echo "<li><h2>#$c_name</h2></li>";
					}
				?>
				<li>
					<label id="newChannelButton">
						<input type="checkbox" onclick="jsfunction()" href="javascript:void(0);">
						<span>+</span>
						<div id="newChannelFormOuter">
							<div id="newChannelForm">
								<h2>New Channel</h2>
								<form action="home.php" method="post">
									<input id="newChannelName" type="text" name="channelName" autocomplete="off">
								</form>
								<label for="newChannelName">#</label>
							</div>
						</div>
					</label>
				</li>
			</ul>
		</div>
		<div class="column-right">
		</div>
	</body>
</html>