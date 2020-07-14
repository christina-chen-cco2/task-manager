<?php

	include "../config.php";
	
	
	if ($_POST["action"] === "load") {
		load();
	} else if ($_POST["action"] === "save") {
		save();
	} else if ($_POST["action"] === "edit") {
		edit();
	} else if ($_POST["action"] === "delete") {
		delete();
	} else if ($_POST["action"] === "checkbox") {
		checkbox();
	}
	
	
	function load() {
		$mysqli = new mysqli(HOST, USER, PASS, DB);
		if ($mysqli->connect_error) {
			die("Connection error: " . $mysqli->connect_error);
		}
		$statement = $mysqli->prepare(
			"SELECT id, name, time, duration, place, description FROM TODO WHERE creatorId=? AND completed=? ORDER BY date"
		);
		$statement->bind_param("ii", $creatorId, $completed);
		$creatorId = 11;
		$completed = $_POST["completed"];
		
		$array = array("taskId" => array(), "taskName" => array(), "taskTime" => array(), "taskDuration" => array(), "taskPlace" => array(),
			"taskDescription" => array(), "completedNum" => null);
		
		if ($statement->execute()) {
			$statement->bind_result($id, $name, $time, $duration, $place, $description);
			while ($statement->fetch()) {
				$time = $time === null ? "" : $time;
				$duration = $duration === null ? "" : $duration;
				$place = $place === null ? "" : $place;
				$description = $description === null ? "" : $description;
				array_push($array["taskId"], $id);
				array_push($array["taskName"], $name);
				array_push($array["taskTime"], $time);
				array_push($array["taskDuration"], $duration);
				array_push($array["taskPlace"], $place);
				array_push($array["taskDescription"], $description);
			}
			$statement->close();
		} else {
			echo $mysqli->error;
		}
		
		$statement = $mysqli -> prepare("SELECT COUNT(completed) AS completedNum FROM TODO WHERE completed=true");
		
		if ($statement->execute()) {
			$statement->bind_result($completedNum);
			$statement->fetch();
			$array["completedNum"] = $completedNum;
			$statement->close();
		} else {
			echo $mysqli->error;
		}
		
		echo json_encode($array);
		$mysqli->close();
	}
	
	function save() {
		$mysqli = new mysqli(HOST, USER, PASS, DB);
		if ($mysqli->connect_error) {
			die("Connection error: " . $mysqli->connect_error);
		}
		
		if (empty($_POST["taskId"])) {
			$statement = $mysqli->prepare(
				"INSERT INTO TODO (date, name, time, duration, place, description, userId)
			VALUES (?, ?, ?, ?, ?, ?, ?)");
			$statement->bind_param("sssssss", $date, $name, $time, $duration, $place, $description, $userId);
			$date = time();
			$userId = 11;
		} else {
			$statement = $mysqli->prepare(
				"UPDATE TODO SET name=?, time=?, duration=?, place=?, description=? WHERE id=?");
			$statement->bind_param("sssssi",$name, $time, $duration, $place, $description, $taskId);
			$taskId = $_POST["taskId"];
		}
		
		$name = $_POST["name"];
		$time = $_POST["time"];
		$duration = $_POST["duration"];
		$place = $_POST["place"];
		$description = $_POST["description"];
		
		if ($statement->execute()) {
			$statement->close();
		} else {
			echo $mysqli->error;
		}
		$mysqli->close();
	}
	
	function edit() {
		$mysqli = new mysqli(HOST, USER, PASS, DB);
		if ($mysqli->connect_error) {
			die("Connection error: " . $mysqli->connect_error);
		}
		
		$statement = $mysqli->prepare("SELECT name, time, duration, place, description FROM TODO WHERE id=?");
		$statement->bind_param("i", $taskId);
		$taskId = $_POST["taskId"];
		if ($statement->execute()) {
			$statement->bind_result($name, $time, $duration, $place, $description);
			$statement->fetch();
			echo json_encode(
				array("name" => $name, "time" => $time, "duration" => $duration, "place" => $place, "description" => $description));
			$statement->close();
		} else {
			$mysqli->error;
		}
		$mysqli->close();
	}
	
	function delete() {
		$mysqli = new mysqli(HOST, USER, PASS, DB);
		if ($mysqli->connect_error) {
			die("Connection error: " . $mysqli->connect_error);
		}
		
		$statement = $mysqli->prepare("DELETE FROM TODO WHERE id=?");
		$statement->bind_param("i", $taskId);
		$taskId = $_POST["taskId"];
		
		if ($statement->execute()) {
			$statement->close();
		} else {
			echo $mysqli->error;
		}
		$mysqli->close();
	}
	
	function checkbox() {
		$mysqli = new mysqli(HOST, USER, PASS, DB);
		if ($mysqli->connect_error) {
			die("Connection error: " . $mysqli->connect_error);
		}
		
		$statement = $mysqli->prepare("UPDATE TODO SET completed=? WHERE id=?");
		$statement->bind_param("ii", $completed, $taskId);
		
		$completed = $_POST["completed"];
		$taskId = $_POST["taskId"];
		
		if ($statement->execute()) {
			$statement->close();
		} else {
			echo $mysqli->error;
		}
		
		$statement = $mysqli->prepare("SELECT COUNT(completed) AS completedNum FROM TODO WHERE completed=1");
		
		if ($statement->execute()) {
			$statement->bind_result($completedNum);
			$statement->fetch();
			$statement->close();
		} else {
			echo $mysqli->error;
		}
		
		echo json_encode($completedNum);
		$mysqli->close();
	}
