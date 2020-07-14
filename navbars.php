<?php
	
	
	function generalNavbar($person) {
		
		if ($person == "user") {
			$htmlOutput = <<<EOL
				<div class="ui raised vertical segment">
					<div class="ui small secondary menu" style="margin: 0 10px 0 0;">
						<div class="header item">
							<div class="ui medium header">
								<a href="index.php" style="color: #262626">Task Manager</a>
							</div>
						</div>
						<div class="right menu">
							<a class="ui item" href="/task-manager/todo/todo.html">My To-do</a>
							<a class="ui item" href="/task-manager/projects/projects.html">Projects</a>
							<div class="ui dropdown item"><div class="ui floating dropdown">
								<i class="big user circle icon"></i>
								<div class="ui menu">
									<a class="item">Account</a>
									<a class="item" href="?page=logout">Logout</a>
								</div>
							</div></div>
						</div>
					</div>
				</div>
				<script>
					$('.ui.dropdown').dropdown();
				</script>
EOL;
		} else {
			$htmlOutput = <<<EOL
				<div class="ui raised vertical segment">
					<div class="ui small secondary menu" style="margin: 0 10px 0 5px">
						<div class="header item">
							<div class="ui medium header">
								<a href="index.php" style="color: #262626">Task Manager</a>
							</div>
						</div>
						<div class="right menu">
							<a class="ui item" href="index.php">Home</a>
							<a class="ui item" href="?page=signup">Create Account</a>
							<a class="ui item" href="?page=login">Login</a>
						</div>
					</div>
				</div>
EOL;
		}
		echo $htmlOutput;
	}
	
	/*dropdown project board -- settings (team and add, name, delete, planned date, set project as completed)*/
	
	function projectNavbar($projectName) {
		
		$htmlOutput = <<<EOL
		<div class="ui vertical segment">
			<div class="ui secondary menu" style="margin: 0 10px 0 0">
				<div class="header item">
					<div class="ui large header">
						<a href="?page=projectHome" style="color: #262626">$projectName</a>
					</div>
				</div>
				<div class="right menu">
					<a class="ui item" href="?page=projectBoard">Project Board</a>
					<a class="ui item">Schedule</a>
					<a class="ui item" href="?page=chatRoom">Chat Room</a>
				</div>
			</div>
		</div>
EOL;
		echo $htmlOutput;
	}
