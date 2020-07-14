<?php

	include "../config.php";
	
	if ($_POST["action"] === "getTasklists") {
		getData("getTasklists");
	} else if ($_POST["action"] === "getLabels") {
		getData("getLabels");
	} else if ($_POST["action"] === "getUsernames") {
		getData("getUsernames");
	}
	
	function getData($component) {
		$mysqli = new mysqli(HOST, USER, PASS, DB);
		if ($mysqli->connect_error) {
			die("Connection error: " . $mysqli->connect_error);
		}

		$statement = "";
		if ($component === "getTasklists") {
			$statement = $mysqli->prepare("SELECT id, tasklistName FROM TASKLISTS ORDER BY id");
		} else if ($component === "getLabels") {
			$statement = $mysqli->prepare("SELECT labelName, color FROM LABELS");
		} else if ($component === "getUsernames") {
			$statement = $mysqli->prepare("SELECT username FROM USERS, USERS_PROJECTS WHERE projectId=? AND USERS.id=userId");
			$statement->bind_param("i", $projectId);
			$projectId = 55;
		}
		
		$array = array();
		if ($statement->execute()) {
			if ($component === "getTasklists") {
				$statement->bind_result($tasklistId, $tasklistName);
				while ($statement->fetch()) {
					array_push($array, array("tasklistId" => $tasklistId, "tasklistName" => $tasklistName));
				}
			} else if ($component === "getLabels") {
				$statement->bind_result($labelName, $color);
				while ($statement->fetch()) {
					array_push($array, array("labelName" => $labelName, "color" => $color));
				}
			} else if ($component === "getUsernames") {
				$statement->bind_result($username);
				while ($statement->fetch()) {
					array_push($array, $username);
				}
			}
			$statement->close();
		} else {
			echo $mysqli->error;
		}

		echo json_encode($array);
	}
