var http = require("http");
http.createServer(function (request, response) {

   // Gui HTTP header cua request
   // HTTP Status: 200 : OK
   // Content Type: text/plain
   response.writeHead(200, {'Content-Type': 'text/plain'});
   
   // Gui phan than cua response, bao gom "Hello World"
   response.end('Hello World\n');
}).listen(8081);

// Man hinh Console se in thong bao
console.log('Server dang chay tai http://127.0.0.1:8081/');