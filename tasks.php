<!DOCTYPE html>
<html lang="en">
<head>
	<title>Task Manager</title>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.13/semantic.min.css">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.13/semantic.min.js"></script>
	<script src="https://cdn.tiny.cloud/1/hzn5qpwctuac6f375d02wg884kqjx3ajjndphdscjvj54gaq/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
	<script src='https://cdn.tiny.cloud/1/hzn5qpwctuac6f375d02wg884kqjx3ajjndphdscjvj54gaq/tinymce/5/tinymce.min.js' referrerpolicy="origin"></script>
	<script>
        tinymce.init({
            selector: '#description',
	        toolbar:
		        'undo redo | styleselect | bold italic underline | alignleft aligncenter alignright alignjustify'
        });
	</script>
	<link rel="stylesheet" href="style/stylesheet.css">
</head>

<body>


<?php
	
	
	$newTaskCard = <<<EOL
		<div class="ui centered fluid raised card">
			<div class="content">
				<select multiple="" class="ui floating right floated dropdown" name="label">
					<option>Choose Assignees</option><i class="dropdown icon"></i>
					<option><i class="ui user circle icon"></i>Christina</option>
					<option><i class="ui user circle icon"></i>John Doe</div>
					<option><i class="ui user circle icon"></i>Jill</div>
				</select>
			</div>
			<div class="content">
				<div class="ui fluid transparent huge input">
					<input style="text-align: center" type="text" placeholder="Enter Task Name" id="taskName">
				</div><br/>
				<div class="ui form">
					<div class="field">
						<textarea style="width: 100%" rows="20" id="description"></textarea>
					</div>
				</div>
			</div>
			<div class="content">
				<div class="ui selection floating compact dropdown">
					<input type="hidden" id="board">
					<div class="default text">Choose Board</div><i class="dropdown icon"></i>
					<div class="menu">
						<div class="item" data-value="1"><div class="ui label">To-Do</div></div>
						<div class="item" data-value="2"><div class="ui label">In Progress</div></div>
						<div class="item" data-value="3"><div class="ui label">Completed</div></div>
					</div>
				</div>
				<div class="ui selection floating compact dropdown">
					<input type="hidden" id="label">
					<div class="default text">Choose Label</div><i class="dropdown icon"></i>
					<div class="menu">
						<div class="item" data-value="1"><div class="ui pink label">Critical</div></div>
						<div class="item" data-value="2"><div class="ui yellow label">Moderate</div></div>
						<div class="item" data-value="3"><div class="ui teal label">Low</div></div>
					</div>
				</div>
				<button type="button" class="ui right floated primary button" onclick="submitTask()">Save</button>
			</div>
		</div>

	<script>
		$('.ui.dropdown').dropdown();
		
		function submitTask() {
		    const request = new XMLHttpRequest();
		    request.onreadystatechange = function() {
                if (request.readyState === 4 && request.status === 200) {
	                callback(request.response);
		        }
		    };
            const assigneeName = document.getElementById("assigneeName").value;
            const taskName = document.getElementById("taskName").value;
            const description = document.getElementById("description").value;
            const board = document.getElementById("board").value;
            const label = document.getElementById("label").value;
            const data =
                `assigneeName=\${assigneeName}&taskName=\${taskName}&description=\${description}&board=\${board}&label=\${label}`;
            request.open("POST", "phpScripts/tasksSubmit.php?" + data, true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		    request.send(data);
		}
		
		function callback(data) {
		    const parsedData = JSON.parse(data);
		    const board = parseInt(parsedData.board, 10) === 1 ? "red" : parseInt(parsedData.board, 10) === 2 ? "yellow" : "green";
		    const label = parseInt(parsedData.label, 10) === 1 ? "<div class='ui label'>Deciding</div>"
		                    : parseInt(parsedData.label, 10) === 2 ? "<div class='ui label'>Suggestions</div>"
		                        : "<div class='ui label'>Discussion</div>";
            const newCard =
				`<div class="ui centered fluid raised \${board} card" id="createdTaskCard">
					<div class="content">
						<div class="header right floated">
							\${parsedData.assigneeName} <i class="user circle outline icon"></i>
						</div>
					</div>
					<div class="center aligned content"><b>\${parsedData.taskName}</b></div>
					<div class="content">\${parsedData.description}</div>
					<div class="extra content">
						<i class="\${board} large square icon"></i>
						\${label}
						<div class="right floated text">#21</div>
					</div>
				</div>`;
			document.getElementById("createdTasks").insertAdjacentHTML("afterbegin", newCard);
			$('#createdTaskCard').transition('hide');
			$('#createdTaskCard').transition({ animation: 'scale', duration: 300 });
		}

	</script>
EOL;
	
	$statusMessage = <<<EOL
	<div class="ui warning message">
	<div class="ui grid">
		<div class="eight wide column">
			Labels
			<div class="ui bulleted list">
				<div class="item">1 suggestion</div>
			</div>
		</div>
		<div class="eight wide column">
			Boards
			<div class="ui bulleted list">
				<div class="item">1 To-Do</div>
			</div>
			<div class="ui bulleted list">
				<div class="item">5 Completed</div>
			</div>
		</div>
	</div>
</div>
EOL;
	
	
	$htmlOutput = <<<EOL
	<div style="position: relative">
		<div class="ui internally celled very relaxed grid" style="position: absolute; height: 84vh">
			<div class="six wide column" style="overflow-y: auto">
				<div class="ui huge center aligned header">All Tasks<div class="ui large circular label">6</div></div>
				<div class="ui padded divider"></div><br/>
				<div id="createdTasks"></div>
			</div>
			<div class="ten wide column" style="overflow-y: auto">
				$statusMessage
				$newTaskCard
			</div>
		</div>
	</div>
EOL;
	
	
	echo $htmlOutput;
	
	
?>





</body>
</html>

