<?php
	
	include "../config.php";
	
	if ($_POST["action"] === "getUsernames") {
		getUsernames();
	} else if ($_POST["action"] === "loadProjects") {
		loadProjects();
	} else if ($_POST["action"] === "createProject")  {
		createProject();
	}
	
	
	function getUsernames() {
		$mysqli = new mysqli(HOST, USER, PASS, DB);
		if ($mysqli->connect_error) {
			die("Connection error: " . $mysqli->connect_error);
		}
		
		$statement = $mysqli->prepare("SELECT username FROM USERS WHERE id != ?");
		$statement->bind_param("i", $selfId);
		
		$selfId = 11;
		
		$array = array();
		if ($statement->execute()) {
			$statement->bind_result($username);
			while ($statement->fetch()) {
				array_push($array, $username);
			}
			$statement->close();
		} else {
			echo $mysqli->error;
		}
		
		echo json_encode($array);
	}
	
	
	function createProject() {
		$mysqli = new mysqli(HOST, USER, PASS, DB);
		if ($mysqli->connect_error) {
			die("Connection error: " . $mysqli->connect_error);
		}
		
		$statementProjects = $mysqli->prepare(
			"INSERT INTO PROJECTS (date, projectName, plannedDate, completedDate) VALUES (?, ?, ?, ?)");
		$statementProjects->bind_param("isii", $date, $projectName, $plannedDate, $completedDate);
		$date = time();
		$projectName = $_POST["projectName"];
		$creatorId = 11;
		$plannedDate = strtotime($_POST["plannedDate"]);
		$completedDate = -1;
		
		if ($statementProjects->execute()) {
			$statementProjects->close();
		} else {
			echo $mysqli->error;
		}
		
		$statementInsertCreator = $mysqli->prepare("INSERT INTO USERS_PROJECTS (userId, projectId) VALUES (?, ?)");
		$statementInsertCreator->bind_param("si", $creatorId, $projectId);
		$projectId = $mysqli->insert_id;
		
		if ($statementInsertCreator->execute()) {
			$statementInsertCreator->close();
		} else {
			echo $mysqli->error;
		}
		
		if (!empty($_POST["teamMembers"])) {
			$statementUsersProjects = $mysqli->prepare("INSERT INTO USERS_PROJECTS (userId, projectId) VALUES (?, ?)");
			$statementGetTeamMemberIds = $mysqli->prepare("SELECT id FROM USERS WHERE username=?");
			$statementGetTeamMemberIds->bind_param("s", $teamMemberUsername);
			$statementUsersProjects->bind_param("si", $teamMemberId, $projectId);
			
			$teamMembers = explode(",", $_POST["teamMembers"]);
			foreach ($teamMembers as $teamMemberUsername) {
				if ($statementGetTeamMemberIds->execute()) {
					$statementGetTeamMemberIds->store_result();
					$statementGetTeamMemberIds->bind_result($teamMemberId);
					$statementGetTeamMemberIds->fetch();
				} else {
					echo $mysqli->error;
				}
				if (!$statementUsersProjects->execute()) {
					echo $mysqli->error;
				}
			}
			$statementGetTeamMemberIds->close();
			$statementUsersProjects->close();
		}
		
		echo 1;
	}
	
	
	function loadProjects() {
		$mysqli = new mysqli(HOST, USER, PASS, DB);
		if ($mysqli->connect_error) {
			die("Connection error: " . $mysqli->connect_error);
		}
		
		$statement = $mysqli->prepare(
			"SELECT PROJECTS.id, PROJECTS.date, projectName, plannedDate, completedDate
			FROM PROJECTS, USERS_PROJECTS
			WHERE PROJECTS.id = USERS_PROJECTS.projectId AND USERS_PROJECTS.userId = ?");
		$statement->bind_param("i", $userId);
		$userId = 11;
		
		$array = array();
		if ($statement->execute()) {
			$statement->bind_result($projectId, $date, $projectName, $plannedDate, $completedDate);
			while ($statement->fetch()) {
				array_push($array, array(
					"projectId" => $projectId, "startedDate" => $date, "projectName" => $projectName,
					"plannedDate" => $plannedDate, "completedDate" => $completedDate)
				);
			}
			echo json_encode($array);
			$statement->close();
		} else {
			$mysqli->error;
		}

		$mysqli->close();
	}
	
	
?>
