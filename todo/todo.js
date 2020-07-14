$(document).ready(function () {

    loadTasks(true);

});

function loadTasks(forUncompleted) {
    $('#calendar').calendar({ popupOptions: { observeChanges: false } });

    const request = new XMLHttpRequest();
    request.onreadystatechange = function () {
        if (request.readyState === 4 && request.status === 200) {
            const data = JSON.parse(request.response);
            for (let i = 0; i < data.taskId.length; i++) {
                const taskId = data.taskId[i];
              //  const tempDiv = document.createElement("div");
                document.body.appendChild(document.getElementById("taskTemplate").cloneNode(true).content);
             //   tempDiv.appendChild(document.getElementById("task"));
                document.getElementById("todo").appendChild(document.getElementById("task"));
                document.body.appendChild(document.getElementById("popup").cloneNode(true));
             //   tempDiv.appendChild(document.getElementById("popup"));
                document.body.appendChild(document.getElementById("popup"));

                const taskDescription = data.taskDescription[i] !== `` ? data.taskDescription[i] : ``;
                const taskTime =
                    data.taskTime[i] !== ``
                        ? `<i class='small calendar alternate icon'></i><span style='font-size: 13px;'>${data.taskTime[i]}</span><br/>`
                        : ``;
                const taskDuration =
                    data.taskDuration[i] !== ``
                        ? `<i class='small clock outline icon'></i><span style='font-size: 13px;'>${data.taskDuration[i]}</span><br/>`
                        : ``;
                const taskPlace =
                    data.taskPlace[i] !== ``
                        ? `<i class='small marker alternate icon'></i><span style='font-size: 13px;'>${data.taskPlace[i]}</span>`
                        : ``;

                const array = ["taskName", "taskDescription", "taskTime", "taskDuration", "taskPlace",
                    "popupName", "popupDescription", "popupTime", "popupDuration", "popupPlace",
                    "popupSave", "popupDelete", "popup", "checkbox", "task", "calendar"
                ];

                for (let i = 0; i < array.length; i++) {
                    document.getElementById(array[i]).id = array[i] + taskId;
                }

                document.getElementById(`checkbox${taskId}`).onclick = function () { checkbox(taskId) };
                document.getElementById(`task${taskId}`).onclick = function () { editTask(taskId) };
                document.getElementById(`popupSave${taskId}`).onclick = function () { saveTask(taskId) };
                document.getElementById(`popupDelete${taskId}`).onclick = function () { deleteTask(taskId) };

                $(`#calendar${taskId}`).calendar({ popupOptions: { observeChanges: false } });

                document.getElementById(`taskName${taskId}`).innerHTML = data.taskName[i];
                document.getElementById(`taskDescription${taskId}`).innerHTML = taskDescription;
                document.getElementById(`taskTime${taskId}`).innerHTML = taskTime;
                document.getElementById(`taskDuration${taskId}`).innerHTML = taskDuration;
                document.getElementById(`taskPlace${taskId}`).innerHTML = taskPlace;
                document.getElementById(`popupName${taskId}`).value = data.taskName[i];
                document.getElementById(`popupDescription${taskId}`).value = taskDescription;
                document.getElementById(`popupTime${taskId}`).value = taskTime;
                document.getElementById(`popupDuration${taskId}`).value = taskDuration;
                document.getElementById(`popupPlace${taskId}`).value = taskPlace;

                if (!forUncompleted) {
                    $(`#task${taskId}`).transition('hide').transition({ animation: 'slide down' });
                    document.getElementById(`task${taskId}`).style.backgroundColor = "#F8F8F8";
                    document.getElementById("checkbox" + taskId).innerHTML = "<i class='check black icon'></i>";
                    document.getElementById("checkbox" + taskId).style.border = "1px solid lightblue";
                }
            }
            document.getElementById("completedNum").innerHTML = data.completedNum;
        }
    }
    if (forUncompleted) {
        request.open("POST", "todo.php?action=load&completed=0", true);
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.send("action=load&completed=0");
    } else {
        request.open("POST", "todo.php?action=load&completed=1", true);
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.send("action=load&completed=1");
    }
}

function addTask() {
   // const tempDiv = document.createElement("div");
    document.body.appendChild(document.getElementById("taskTemplate").cloneNode(true).content);
  //  tempDiv.appendChild(document.getElementById("task"));
    document.getElementById("todo").appendChild(document.getElementById("task"));

    document.getElementById("checkbox").onclick = null;

    $('#task').popup({ popup: '#popup', on: 'click', position: 'right center' });
    document.getElementById("task").click();

    document.getElementById("plus").style.pointerEvents = 'none';
    document.getElementById("plus").style.color = 'lightgray';
}


function editTask(taskId) {
    const request = new XMLHttpRequest();
    request.onreadystatechange = function () {
        if (request.readyState === 4 && request.status === 200) {
            const parsed = JSON.parse(request.response);
            document.getElementById(`popupName${taskId}`).value = parsed.name;
            document.getElementById(`popupTime${taskId}`).value = parsed.time;
            document.getElementById(`popupDuration${taskId}`).value = parsed.duration;
            document.getElementById(`popupPlace${taskId}`).value = parsed.place;
            document.getElementById(`popupDescription${taskId}`).value = parsed.description;
            $(`#task${taskId}:not(#checkbox${taskId})`)
                .popup({ popup: `#popup${taskId}`, on: 'click', position: 'right center' });
        }
    };
    request.open("POST", `todo.php?action=edit&taskId=${taskId}`, true);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    request.send(`action=edit&taskId=${taskId}`);
}


function saveTask(taskId) {
    let request = new XMLHttpRequest();
    let name = document.getElementById(`popupName${taskId}`).value;
    let time = document.getElementById(`popupTime${taskId}`).value;
    let duration = document.getElementById(`popupDuration${taskId}`).value;
    let place = document.getElementById(`popupPlace${taskId}`).value;
    let description = document.getElementById(`popupDescription${taskId}`).value;
    let data = `taskId=${taskId}&name=${name}&time=${time}&duration=${duration}&place=${place}&description=${description}`;

    request.onreadystatechange = function () {
        if (request.readyState === 4 && request.status === 200) {
            description = description !== null ? description : "";
            time =
                time !== ``
                    ? `<i class="small calendar alternate icon"></i><span style="font-size: 13px;">${time}</span><br/>`
                    : ``;
            duration =
                duration !== ``
                    ? `<i class="small clock outline icon"></i><span style="font-size: 13px;">${duration}</span><br/>`
                    : "";
            place =
                place !== ``
                    ? `<i class="small marker alternate icon"></i><span style="font-size: 13px;">${place}</span><br/>`
                    : ``;
            document.getElementById(`taskName${taskId}`).innerHTML = name;
            document.getElementById(`taskTime${taskId}`).innerHTML = time;
            document.getElementById(`taskDuration${taskId}`).innerHTML = duration;
            document.getElementById(`taskPlace${taskId}`).innerHTML = place;
            document.getElementById(`taskDescription${taskId}`).innerHTML = description;

            document.getElementById("plus").style.pointerEvents = 'auto';
            document.getElementById("plus").style.color = '#4D4D4D';
            $(`#task${taskId}`).popup('hide');
        }
    };
    request.open("POST", "todo.php?action=save&" + data, true);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    request.send("action=save&" + data);
}


function deleteTask(taskId) {
    if (taskId !== "") {
        const request = new XMLHttpRequest();
        request.onreadystatechange = function () {
            if (request.readyState === 4 && request.status === 200) {
                $(`#task${taskId}`).transition({ animation: 'slide up', onComplete: function () {
                        document.getElementById("popup" + taskId).remove();
                        document.getElementById("task" + taskId).remove();
                    }
                });
            }
        };
        request.open("POST", `todo.php?action=delete&taskId=${taskId}`, true);
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.send(`action=delete&taskId=${taskId}`);
    } else {
        $(`#task${taskId}`).transition({ animation: 'slide up', onComplete: function () {
                $(`#task${taskId}`).popup('hide');
                document.getElementById("task" + taskId).remove();
                document.getElementById("plus").style.pointerEvents = 'auto';
                document.getElementById("plus").style.color = 'black';
            }
        });
    }
}


function checkbox(taskId) {
    if (document.getElementById("checkbox" + taskId).innerHTML === "") {
        const request = new XMLHttpRequest();
        request.onreadystatechange = function () {
            if (request.readyState === 4 && request.status === 200) {
                document.getElementById("checkbox" + taskId).innerHTML = "<i class='check black icon'></i>";
                document.getElementById("checkbox" + taskId).style.border = "1px solid lightblue";
                $(`#task${taskId}`).transition({
                    animation: 'slide up', duration: 500, onComplete: function () {
                        $(`#task${taskId}`).popup('hide');
                        document.getElementById("task" + taskId).remove();
                    }
                });
                document.getElementById("completedNum").innerHTML = JSON.parse(request.response);
            }
        }
        request.open("POST", `todo.php?action=checkbox&taskId=${taskId}&completed=1`, true);
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.send(`action=checkbox&taskId=${taskId}&completed=1`);
    } else {
        document.getElementById("checkbox" + taskId).innerHTML = "";
        document.getElementById("checkbox" + taskId).style.border = "1px solid lightgray";
    }
}
