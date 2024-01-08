//webkitURL is deprecated but nevertheless
URL = window.URL || window.webkitURL;

var gumStream; 						//stream from getUserMedia()
var rec; 							//Recorder.js object
var input; 							//MediaStreamAudioSourceNode we'll be recording

// shim for AudioContext when it's not avb. 
var AudioContext = window.AudioContext || window.webkitAudioContext;
var audioContext; //audio context to help us record

var recordButton = document.getElementById("recordButton");
var stopButton = document.getElementById("stopButton");
var pauseButton = document.getElementById("pauseButton");
var audioRecorder = document.getElementById("audio-recorder");
var recordingInfo = document.getElementById("recording-info");

resetRecorder();

var audioRecorderModal = document.getElementById('audioRecorderModal');
audioRecorderModal.addEventListener('show.bs.modal', function (event) {
	resetRecorder();
});

function resetRecorder() {
	recordingInfo.style.display = "none";
	stopButton.style.display = "none";
	pauseButton.style.display = "none";
	recordButton.style.display = "block";
	audioRecorder.classList.remove('is-recording');
	stopButton.disabled = true;
	recordButton.disabled = false;
	pauseButton.disabled = true;
	if (recordingsList.hasChildNodes()) {
		recordingsList.removeChild(recordingsList.children[0]);
	}
}


var elapsedTimeTag = document.getElementsByClassName("elapsed-time")[0];
var audioRecordStartTime;
var maximumRecordingTimeInMins = 15;
var elapsedTimeTimer;
var isPaused = false;

//add events to those 2 buttons
recordButton.addEventListener("click", startRecording);
stopButton.addEventListener("click", stopRecording);
pauseButton.addEventListener("click", pauseRecording);
function displayAudioTimer(elapsedTime) {
	console.log('displayAudioTimer', elapsedTime);
	elapsedTimeTag.innerHTML = elapsedTime;
	if (isElapsedTimeReached(elapsedTime)) {
		stopRecording();
	}
}
function isElapsedTimeReached(elapsedTime) {
	let elapsedTimeSplitted = elapsedTime.split(":");
	let maximumRecordingTimeInMinsAsString = maximumRecordingTimeInMins < 10 ? "0" + maximumRecordingTimeInMins : maximumRecordingTimeInMins.toString();
	if (elapsedTimeSplitted.length === 2 && elapsedTimeSplitted[0] === maximumRecordingTimeInMinsAsString)
		return true;
	else //otherwise, return false
		return false;
}
function computeElapsedTime(startTime) {

	let endTime = new Date();

	let timeDiff = endTime - startTime;
	timeDiff = timeDiff / 1000;
	let seconds = Math.floor(timeDiff % 60); //ignoring uncomplete seconds (floor)

	//pad seconds with a zero if neccessary
	seconds = seconds < 10 ? "0" + seconds : seconds;

	//convert time difference from seconds to minutes using %
	timeDiff = Math.floor(timeDiff / 60);

	//extract integer minutes that don't form an hour using %
	let minutes = timeDiff % 60; //no need to floor possible incomplete minutes, becase they've been handled as seconds
	minutes = minutes < 10 ? "0" + minutes : minutes;

	//convert time difference from minutes to hours
	timeDiff = Math.floor(timeDiff / 60);

	//extract integer hours that don't form a day using %
	let hours = timeDiff % 24; //no need to floor possible incomplete hours, becase they've been handled as seconds

	//convert time difference from hours to days
	timeDiff = Math.floor(timeDiff / 24);

	// the rest of timeDiff is number of days
	let days = timeDiff; //add days to hours

	let totalHours = hours + (days * 24);
	totalHours = totalHours < 10 ? "0" + totalHours : totalHours;

	if (totalHours === "00") {
		return minutes + ":" + seconds;
	} else {
		return totalHours + ":" + minutes + ":" + seconds;
	}
}

function handleElapsedRecordingTime() {
	displayAudioTimer("00:00");
	// Calculate the initial elapsed time considering both recorded time and paused time
	let initialElapsedTime = computeElapsedTime(audioRecordStartTime - pausedTime * 1000);
	displayAudioTimer(initialElapsedTime);

	// Update the timer every second
	elapsedTimeTimer = setInterval(() => {
		if (!isPaused) {
			let elapsedTime = computeElapsedTime(audioRecordStartTime);
			displayAudioTimer(elapsedTime);
		}
	}, 1000);
}

function startRecording() {
	console.log("recordButton clicked");
	var constraints = { audio: true, video: false };
	recordButton.disabled = true;
	stopButton.disabled = false;
	pauseButton.disabled = false;

	navigator.mediaDevices.getUserMedia(constraints).then(function (stream) {
		console.log("getUserMedia() success, stream created, initializing Recorder.js ...");
		audioContext = new AudioContext();

		gumStream = stream;
		input = audioContext.createMediaStreamSource(stream);

		// Ensure pausedTime is defined
		if (typeof pausedTime === 'undefined') {
			pausedTime = 0;
		}

		if (isPaused) {
			// If recording is paused, calculate the total elapsed time including pause time
			var totalTimeElapsed = (new Date() - audioRecordStartTime) / 1000;
			audioRecordStartTime = new Date() - (pausedTime * 1000);
		} else {
			// If not paused, reset paused time and start recording from the beginning
			pausedTime = 0;
			audioRecordStartTime = new Date();
		}

		rec = new Recorder(input, { numChannels: 1 });
		rec.record();
		audioRecorder.classList.add('is-recording');
		console.log("Recording started");
		recordButton.style.display = "none";
		recordingInfo.style.display = "block";
		stopButton.style.display = "inline-block";
		pauseButton.style.display = "inline-block";
		handleElapsedRecordingTime();

	}).catch(function (err) {
		// enable the record button if getUserMedia() fails
		console.log('err', err);
		audioRecorder.classList.remove('is-recording');
		recordButton.disabled = false;
		stopButton.disabled = true;
		pauseButton.disabled = true;
	});
}


function pauseRecording() {
	console.log("pauseButton clicked rec.recording=", rec.recording);
	if (rec.recording) {
		rec.stop();
		isPaused = true;
		// Capture the timestamp when recording is paused
		pausedTime = (new Date() - audioRecordStartTime) / 1000;
	} else {
		// Adjust the start time by subtracting the time elapsed during pause
		audioRecordStartTime = new Date() - (pausedTime * 1000);
		rec.record();
		isPaused = false;
	}
}


function stopRecording() {
	console.log("stopButton clicked");
	clearInterval(elapsedTimeTimer);
	rec.stop();
	gumStream.getAudioTracks()[0].stop();
	rec.exportWAV(createDownloadLink);
	stopButton.disabled = true;
	recordButton.disabled = false;
	pauseButton.disabled = true;
	stopButton.style.display = "none";
	pauseButton.style.display = "none";
	recordButton.style.display = "block";
	audioRecorder.classList.remove('is-recording');


}

function createDownloadLink(blob) {

	var url = URL.createObjectURL(blob);
	var au = document.createElement('audio');
	var audioPreviewDiv = document.createElement('div');
	audioPreviewDiv.setAttribute("class", "audio-player-preview");
	var link = document.createElement('a');
	link.setAttribute("class", "btn btn-primary");
	//name of .wav file to use during upload and download (without extendion)
	var filename = new Date().toISOString();

	//add controls to the <audio> element
	au.controls = true;
	au.src = url;

	//save to disk link
	link.href = url;
	link.download = filename + ".wav"; //download forces the browser to donwload the file using the  filename
	link.innerHTML = '<i class="fa fa-save"></i>';


	audioPreviewDiv.appendChild(au);


	audioPreviewDiv.appendChild(link);

	var upload = document.createElement('a');
	upload.href = "#";
	upload.setAttribute("class", "btn btn-info");
	upload.innerHTML = '<i class="fa fa-cloud-upload"></i>';
	upload.addEventListener("click", function (event) {

		var $fileWrapperElem = $("#audioMediaBox");
		let upload_type = $fileWrapperElem.find('.upload-file-box').data('type');
		var file = new File([blob], uuid() + ".wav");
		var files = [];
		files.push(file);

		handleFileUpload(files, upload_type, $fileWrapperElem);

		var modal = bootstrap.Modal.getInstance(audioRecorderModal);
		modal.hide();
	});

	audioPreviewDiv.appendChild(upload);//add the upload link to li
	if (recordingsList.hasChildNodes()) {
		recordingsList.removeChild(recordingsList.children[0]);
	}
	recordingsList.appendChild(audioPreviewDiv);
}
