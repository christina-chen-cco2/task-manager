$(document).ready(function () {
    addProject();
    loadProjects();

});

function addProject() {
    $("#teamMembersDropdown").dropdown();
    $("#addProjectModal")
        .modal({
            blurring: true,
            onShow: function () { $("#plannedDateCalendar").calendar({ type: "date" }); },
            onApprove: function () { createProject() },
            closable: false,
            transition: "fade up",
        }).modal("attach events", "#addProjectButton", "show");

    const request = new XMLHttpRequest();
    let teamMemberUsernames = "";
    request.onreadystatechange = function () {
        if (request.readyState === 4 && request.status === 200) {
            teamMemberUsernames = JSON.parse(request.response);
            for (let i = 0; i < teamMemberUsernames.length; i++) {
                document.getElementById("teamMembersMenu").innerHTML +=
                    "<div class='item' data-value='" + teamMemberUsernames[i]  + "'>"
                    + "<i class='user icon'></i>"
                    + teamMemberUsernames[i]
                    + "</div>";
            }
        }
    }
    request.open("POST", "projects.php?action=getUsernames", true);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    request.send("action=getUsernames");
}

function createProject() {
    const projectName = document.getElementById("projectName").value;
    const plannedDate = document.getElementById("plannedDate").value;
    const teamMembers = document.getElementById("teamMembers").value;
    const data = `&projectName=${projectName}&plannedDate=${plannedDate}&teamMembers=${teamMembers}`;

    const request = new XMLHttpRequest();
    request.onreadystatechange = function () {
        if (request.readyState === 4 && request.status === 200) {
            console.log(request.response);
            if (parseInt(request.response) === 1) {
                document.getElementById("projectName").value = "";
                $("#plannedDateCalendar").calendar("clear");
                $("#teamMembersDropdown").dropdown("clear");
                document.getElementById("successHeader").innerText = `${projectName} has been successfully created.`;
                $("#successModal").modal("show");
            }
        }
    }
    request.open("POST", `projects.php?action=createProject${data}`, true);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    request.send(`action=createProject${data}`);
}

function loadProjects() {
    const request = new XMLHttpRequest();
    request.onreadystatechange = function () {
        if (request.readyState === 4 && request.status === 200) {
            const data = JSON.parse(request.response);
            for (let i = 0; i < data.length; i++) {
              //  const tempDiv = document.createElement("div");
                document.body.appendChild(document.getElementById("projectTemplate").cloneNode(true).content);
             //   tempDiv.appendChild(document.getElementById("project"));
                document.getElementById("projectList").appendChild(document.getElementById("project"));

                const projectId = data[i].projectId;
                const startedDate = new Date(parseInt(data[i].startedDate) * 1000);
                const plannedDate = new Date(parseInt(data[i].plannedDate) * 1000);

                document.getElementById("project").id = `project${projectId}`;
                document.getElementById("projectNameDisplay").id = `projectNameDisplay${projectId}`;
                document.getElementById("projectStatus").id = `projectStatus${projectId}`;
                document.getElementById("startedDateDisplay").id = `startedDateDisplay${projectId}`;
                document.getElementById("plannedDateDisplay").id = `plannedDateDisplay${projectId}`;

                document.getElementById(`projectNameDisplay${projectId}`).innerHTML = data[i].projectName;
                document.getElementById(`projectStatus${projectId}`).innerHTML
                    = data[i].completedDate === -1 ? "In Progress" : "Completed";
                document.getElementById(`startedDateDisplay${projectId}`).innerText = "Started: "
                    + startedDate.getUTCMonth() + "/" + startedDate.getUTCDay() + "/"
                    + startedDate.getUTCFullYear().toString().substring(0, 2);
                document.getElementById(`plannedDateDisplay${projectId}`).innerText = "Planned: "
                    + plannedDate.getUTCMonth() + "/" + plannedDate.getUTCDay() + "/"
                    + plannedDate.getUTCFullYear().toString().substring(0, 2);
            }
        }
    }
    request.open("POST", "projects.php?action=loadProjects", true);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    request.send("action=loadProjects");
}

