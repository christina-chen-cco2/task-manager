const io = require("socket.io");
const server = io.listen(80);
server.on("connection", (socket) => {
    console.log("hi");
    socket.emit("messaging", "hello client");
})
