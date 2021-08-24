
const express = require('express');
const Peer = require('peer');
const http = require('http');
const app = express();
const server = http.createServer(app);


const customGenerationFunction = () => (Math.random().toString(36) + '0000000000000000000').substr(2, 16);
app.get('/', (req, res, next) => res.send('Hello world!'));
const PeerServer = Peer.ExpressPeerServer(server, {
    path: '/',
    generateClientId: customGenerationFunction
});

app.use('/VoiceChat', PeerServer);
server.listen(8080);



PeerServer.on('connection', (client) => { 
    console.log(client.id);
});

PeerServer.on('disconnect', (client) => { 
    console.log(client.id);
});
