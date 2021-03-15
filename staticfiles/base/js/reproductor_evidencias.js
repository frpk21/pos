// Create a WaveSurfer instance
var wavesurfer_new;

// Init on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    wavesurfer_new = WaveSurfer.create({
        container: '#waveform-evidencias',
        barWidth: 2,
        barHeight: 1, // the height of the wave
        barGap: null,
        waveColor: "#2ECCFA",
        progressColor: "#2E9AFE",
        height: 64
    });
});

// Bind controls
document.addEventListener('DOMContentLoaded', function() {
    var playPause = document.querySelector('#playPause');
    playPause.addEventListener('click', function() {
        wavesurfer_new.playPause();
    });

    // Toggle play/pause text
    wavesurfer_new.on('play', function() {
        document.querySelector('#play').style.display = 'none';
        document.querySelector('#pause').style.display = '';
    });
    wavesurfer_new.on('pause', function() {
        document.querySelector('#play').style.display = '';
        document.querySelector('#pause').style.display = 'none';
    });

    // The playlist links
    var links = document.querySelectorAll('#playlist a');
    var currentTrack = 0;

    // Load a track by index and highlight the corresponding link
    var setCurrentSong = function(index) {
        links[currentTrack].classList.remove('active');
        currentTrack = index;
        links[currentTrack].classList.add('active');
        wavesurfer_new.load(links[currentTrack].href);
    };

    // Load the track on click
    Array.prototype.forEach.call(links, function(link, index) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            setCurrentSong(index);
        });
    });

    // // Play on audio load
    // wavesurfer_new.on('ready', function() {
    //     wavesurfer_new.playPause();
    // });

    wavesurfer_new.on('error', function(e) {
        console.warn(e);
    });

    // Go to the next track on finish
    wavesurfer_new.on('finish', function() {
        setCurrentSong((currentTrack + 1) % links.length);
    });

    // Load the first track
    setCurrentSong(currentTrack);
});