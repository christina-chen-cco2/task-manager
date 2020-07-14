$(document).ready(function () {
    $("#workspace")
        .sidebar({
            onHide: function () { document.getElementById("pusherContent").style.width = "100vw"; },
            onShow: function () { $("#dueDateCalendar").calendar({ type: "date" }); },
            dimPage: false,
            closable: false,
            transition: "overlay",
        }).sidebar("attach events", "button")
    $("#labelDropdown").dropdown();
    $("#userDropdown").dropdown();
    $("#tasklistDropdown").dropdown();
    getTasklists();
    getLabels();
    getUsernames();
    switchPanes("default");
    document.getElementById("currentDate").innerHTML
        = `Assigned: ${new Date().getUTCMonth()}/${new Date().getUTCDate()}`;
});


function allowDrop(event) {
    event.preventDefault();
}

function drag(event) {
    event.dataTransfer.setData("text", event.target.id);
}

function drop(event) {
    event.preventDefault();
    const data = event.dataTransfer.getData("text");
    event.target.appendChild(document.getElementById(data));
}


function getTasklists() {
    const request = new XMLHttpRequest();
    request.onreadystatechange = function () {
        if (request.readyState === 4 && request.status === 200) {
            const data = JSON.parse(request.response);
            const projectBoardWidth = document.getElementById("projectBoard").style.width.replace("vw", "");
            const boardWidth = Math.floor( projectBoardWidth / data.length);
            for (let i = 0; i < data.length; i++) {
                document.getElementById("projectBoardRow").innerHTML +=
                    `<td style="width: ${boardWidth}vw">
                            <div class="ui centered fluid raised card">
                                <div class="content"><h2 class="ui centered aligned header">${data[i].tasklistName}</h2></div>
                                <div class="content" style="padding: 15px;" ondrop="drop(event)" ondragover="allowDrop(event)"></div>
                            </div>
                    </td>`;
                document.getElementById("tasklistMenu").innerHTML +=
                    `<div class="item" data-value="${data[i].tasklistId}">${data[i].tasklistName}</div>`
            }
        }
    }
    request.open("POST", "projectBoard.php?action=getTasklists", true);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    request.send("action=getTasklists");
}

function getLabels() {
    const request = new XMLHttpRequest();
    request.onreadystatechange = function () {
        if (request.readyState === 4 && request.status === 200) {
            const data = JSON.parse(request.response);
            for (let i = 0; i < data.length; i++) {
                document.getElementById("labelMenu").innerHTML +=
                    `<div class="item">
                        <div class="ui tiny label" style="background-color: ${data[i].color}">${data[i].labelName}</div>
                    </div>`;
            }
        }
    }
    request.open("POST", "projectBoard.php?action=getLabels", true);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    request.send("action=getLabels")
}

function getUsernames() {
    const request = new XMLHttpRequest();
    request.onreadystatechange = function () {
        if (request.readyState === 4 && request.status === 200) {
            const data = JSON.parse(request.response);
            for (let i = 0; i < data.length; i++) {
                document.getElementById("userMenu").innerHTML +=
                    `<div class="item">
                        <i class="user icon"></i> ${data[i]}</div>
                    </div>`;
            }
        }
    }
    request.open("POST", "projectBoard.php?action=getUsernames", true);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    request.send("action=getUsernames");

}

function switchPanes(pane) {
    if (pane === "default") {
        document.body.appendChild(document.getElementById("taskCardTemplate").cloneNode(true).content);
        document.getElementById("taskMenuSelection").className = "active item";
        document.getElementById("taskIssuesMenu").appendChild(document.getElementById("taskCard"));
    } else if (pane === "issues" && document.getElementById("issuesMenuSelection").className !== "active item") {
        document.body.appendChild(document.getElementById("issuesFeedTemplate").cloneNode(true).content);
        document.getElementById("issuesMenuSelection").className = "active item";
        document.getElementById("taskMenuSelection").className = "item";
        document.getElementById("taskIssuesMenu")
            .replaceChild(document.getElementById("issuesFeed"), document.getElementById("taskIssuesMenu").childNodes[0]);
    } else if (pane === "task" && document.getElementById("taskMenuSelection").className !== "active item") {
        document.body.appendChild(document.getElementById("taskCardTemplate").cloneNode(true).content);
        document.getElementById("taskMenuSelection").className = "active item";
        document.getElementById("issuesMenuSelection").className = "item";
        document.getElementById("taskIssuesMenu")
            .replaceChild(document.getElementById("taskCard"), document.getElementById("taskIssuesMenu").childNodes[0]);
    }
}