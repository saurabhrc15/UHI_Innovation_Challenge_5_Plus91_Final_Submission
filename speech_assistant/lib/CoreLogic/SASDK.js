var AugnitoSDKEvent = {};
AugnitoSDKEvent.WS_CONNECTING = 1;
AugnitoSDKEvent.MSG_MEDIA_STREAM_CREATED = 2;
AugnitoSDKEvent.MSG_INIT_RECORDER = 3;
AugnitoSDKEvent.MSG_RECORDING = 4;
AugnitoSDKEvent.MSG_SEND_EMPTY = 6;
AugnitoSDKEvent.MSG_WEB_SOCKET_OPEN = 9;
AugnitoSDKEvent.MSG_WEB_SOCKET_CLOSE = 10;
AugnitoSDKEvent.MSG_STOP = 11;

(function (window) {

    var EMPTY = "";
    var INTERVAL = 100;

    // Error codes 
    var ERR_NETWORK = 1;
    var ERR_AUDIO = 2;
    var ERR_SERVER = 3;
    var ERR_CLIENT = 4;

    // Event codes
  
    
    // Server status codes
    var SERVER_STATUS_CODE = {
        0: 'Success', // Usually used when recognition results are sent
        1: 'No speech', // Incoming audio contained a large portion of silence or non-speech
        2: 'Aborted', // Recognition was aborted for some reason
        9: 'No available', // Recognizer processes are currently in use and recognition cannot be performed
    };

    var AugnitoSDK = function (cfg) {

        
        var config = cfg || {};
        config.Server = config.Server || EMPTY;
        config.ContentType = config.ContentType || EMPTY;
        config.AccountCode = config.AccountCode || EMPTY;
        config.AccessKey = config.AccessKey || EMPTY;
        config.LmId = config.LmId || '38';
        config.UserTag = config.UserTag || EMPTY;
        config.LoginToken = config.LoginToken || EMPTY;
        config.NoiseCt = config.NoiseCt || '1';
        config.OtherInfo = config.OtherInfo || EMPTY;
        config.SourceApp = config.SourceApp || EMPTY;
        config.interval = config.interval || INTERVAL;
        config.onReadyForSpeech = config.onReadyForSpeech || function () { };
        config.onPartialResults = config.onPartialResults || function (data) { };
        config.onFinalResults = config.onFinalResults || function (data) { };
        config.OnSessionEvent = config.OnSessionEvent || function (data) { };
        config.onEndOfSession = config.onEndOfSession || function () { };
        config.onEvent = config.onEvent || function (e, data) { };
        config.onError = config.onError || function (e, data) { };
        config.performMicOnAction = config.performMicOnAction || function (e, data) { };
        config.EnableLogs = config.EnableLogs || false;
        config.MacroServiceURL = config.MacroServiceURL || EMPTY;
        config.PushNotification = config.PushNotification || EMPTY;
        config.SpeechMicURL = config.SpeechMicURL || EMPTY;
        config.SpeechMicURL = config.SpeechMicURL || EMPTY;

        // Initialized by init()
        //var audioContext;
        var recorderRTC;
        var ws;
        var audioStream;
        var isConnecting = false;

        // Returns the configuration
        this.getConfig = function () {
            return config;
        }

        // Start recording and transcribing
        this.startListening = function (socketURL) {
            //Create socket connection, If socket success then start recording.
            if (isConnecting) {
                return;
            }
            var currentMicStatus = localStorage.getItem('WebSocketConnectionStatus');
            if (currentMicStatus == "On") {
                config.performMicOnAction();
            }
            else {
                try {
                    isConnecting = true;
                    if (ws) {
                        closeWS();
                    }
                    CreateWebSocket(socketURL);
                }
                catch (e) {
                    ClearAll();
                    isConnecting = false;
                    config.onError(e);
                }
            }
            
        }

        this.toggleListening = function (socketURL) {
            
            if (isConnecting) {
                return;
            }
            
            if (ws) {
                this.stopListening();
            }
            else {
                config.onEvent(AugnitoSDKEvent.WS_CONNECTING, 'Connecting ...');
                this.startListening();
            }
        }

        // Stop listening, i.e. recording and sending of new input.
        this.stopListening = function () {
            if (isConnecting) {
                return;
            }
            try {
                ClearAll();
                config.onEndOfSession();
            } catch (e) {
            }
            isConnecting = false;
        }

        function ClearAll()
        {
            if (ws) {
                localStorage.setItem("WebSocketConnectionStatus", "Off");
            }
            stopStream()
            stopRecorderRTC();
            closeWS();
        }

        function closeWS() {
            try {
                if (ws) {
                    ws.close();
                    ws = null;
                }
            }
            catch (e) {
                console.log(e);
            }
        }

        function stopStream() {
            try {

                if (audioStream) {
                    audioStream.getAudioTracks()[0].stop();
                }
            }
            catch (e) {
                console.log(e)
            }
        }
        function stopRecorderRTC() {
            try {
                if (recorderRTC) {
                    recorderRTC.stopRecording();
                    config.onEvent(AugnitoSDKEvent.MSG_STOP, 'Stopped recorderRTC');
                }
            }
            catch (e) {
                console.log(e);
            }
        }

        function StartAudioStream() {
            // For old browser
            window.AudioContext = window.AudioContext || window.webkitAudioContext;
            navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia;
            var audioSourceConstraints = { audio: true, video: false };
            // for old Browser
            if (navigator.getUserMedia) {
                navigator.getUserMedia(audioSourceConstraints, ProcessAudioStream, function (e) {
                    config.onError(ERR_CLIENT, "No live audio input in this browser: " + e);
                });
            }
            else if (navigator.mediaDevices.getUserMedia) {
                // For safari in mac and new browser
                navigator.mediaDevices.getUserMedia(audioSourceConstraints).then(ProcessAudioStream).catch(function (e) {
                    config.onError(ERR_CLIENT, "No live audio input in this browser: " + e);
                });
            }
            else {
                config.onError(ERR_CLIENT, "No user media support");
            }
        }

        // Private methods
        function ProcessAudioStream(stream) {

            audioStream = stream;
            config.onEvent(AugnitoSDKEvent.MSG_MEDIA_STREAM_CREATED, 'Media stream created');
            window.source = stream;
            var options = {
                mimeType: 'audio/wav',
                type: 'audio',
                checkForInactiveTracks: true,
                noWorker: true,
                noHeaders: true,
                desiredSampRate: 16000,
                timeSlice: config.interval,
                numberOfAudioChannels: 1,
                leftChannel: false,
                disableLogs: true,
                recorderType: StereoAudioRecorder,
                noBlobOnStop: true,
                ondataavailable: function (e) {
                    if (config.EnableLogs) {
                        console.log(e.size + " In interval of(ms)" + config.interval);
                    }
                    socketSend(e);
                }
            };
            recorderRTC = new RecordRTC(stream, options);
            if (!recorderRTC) {
                config.onError(ERR_AUDIO, "Recorder undefined");
                return;
            }
            config.onEvent(AugnitoSDKEvent.MSG_INIT_RECORDER, 'Recorder initialized');
            recorderRTC.startRecording();
            config.onEvent(AugnitoSDKEvent.MSG_RECORDING, 'Recording started');
        }

        function PrepareServerURL() {
            var speechURL = config.Server;
            speechURL = speechURL.concat("?content-type=", config.ContentType);
            speechURL = speechURL.concat("&accountcode=", config.AccountCode)
            speechURL = speechURL.concat("&accesskey=", config.AccessKey);
            speechURL = speechURL.concat("&lmid=", config.LmId);
            speechURL = speechURL.concat("&usertag=", config.UserTag);
            speechURL = speechURL.concat("&logintoken=", config.LoginToken);
            speechURL = speechURL.concat("&noisect=", config.NoiseCt);
            speechURL = speechURL.concat("&otherinfo=", config.OtherInfo);
            speechURL = speechURL.concat("&sourceapp=", config.SourceApp);
            return speechURL;
        }

        function socketSend(item) {
            if (ws) {
                var state = ws.readyState;
                if (state == WebSocket.OPEN) {
                    // If item is an audio blob
                    if (item instanceof Blob) {
                        if (item.size > 0) {
                            ws.send(item);
                        } else {
                            config.onEvent(AugnitoSDKEvent.MSG_SEND_EMPTY, 'Send: blob: ' + item.type + ', EMPTY');
                        }

                    } else {
                        ws.send(item);
                    }
                } else {
                    ClearAll();
                    config.onError(ERR_NETWORK, 'WebSocket: readyState!=1: ' + state + ": failed to send: " + item);
                }
            } else {
                ClearAll();
                config.onError(ERR_CLIENT, 'No web socket connection: failed to send: ' + item);
            }
        }
        //var totalByteSize = 0;
        function CreateWebSocket(socketURL) {

            if (!socketURL) {
                socketURL = PrepareServerURL();
            }
            ws = new WebSocket(socketURL);
            ws.onmessage = function (e) {
                var data = e.data;
                if (data instanceof Object && !(data instanceof Blob)) {
                    config.onError(ERR_SERVER, 'WebSocket: onEvent: got Object that is not a Blob');
                } else if (data instanceof Blob) {
                    config.onError(ERR_SERVER, 'WebSocket: got Blob');
                } else {
                    var res = JSON.parse(data);
                    if ("Status" in res && res.Status == 0) {

                        if ("Type" in res && res.Type == 'meta') {
                            config.OnSessionEvent(res);
                        }
                        else if ("Result" in res) {
                            if (res.Result.Final) {
                                config.onFinalResults(res);
                            } else {
                                config.onPartialResults(res);
                            }
                        }
                    } else {
                        config.onError(ERR_SERVER, 'Server error: ' + res.status + ': ' + getDescription(res.status));
                    }
                }
            }
            // Start recording only if the socket becomes open
            ws.onopen = function (e) {
                StartAudioStream();
                config.onReadyForSpeech();
                config.onEvent(AugnitoSDKEvent.MSG_WEB_SOCKET_OPEN, e);
                isConnecting = false;
            };

            // This can happen if the blob was too big
            // E.g. "Frame size of 65580 bytes exceeds maximum accepted frame size"
            // Status codes
            // http://tools.ietf.org/html/rfc6455#section-7.4.1
            // 1005:
            // 1006:
            ws.onclose = function (e) {

                ClearAll();
                config.onEndOfSession();
                config.onEvent(AugnitoSDKEvent.MSG_WEB_SOCKET_CLOSE, e.code + "/" + e.reason + "/" + e.wasClean);
                isConnecting = false;
            };

            ws.onerror = function (e) {
                var data = e.data;
                ClearAll();
                config.onError(ERR_NETWORK, data);                
                config.onEndOfSession();
                isConnecting = false;
            }

            return ws;
        }

        function getDescription(code) {
            if (code in SERVER_STATUS_CODE) {
                return SERVER_STATUS_CODE[code];
            }
            return "Unknown error";
        }
    };
    window.AugnitoSDK = AugnitoSDK;

})(window);


