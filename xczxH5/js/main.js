  document.addEventListener('DOMContentLoaded', function() {
    var audioPlayer = document.getElementById('audioPlayer');
    var storageKey = 'audioPlayerState';
  
    // 尝试从存储中恢复播放状态
    var savedState = JSON.parse(localStorage.getItem(storageKey));
    if (savedState) {
      audioPlayer.currentTime = savedState.currentTime;
      if (savedState.isPlaying) {
        audioPlayer.play();
      }
    }
  
    // 监听音频的时间更新事件，保存播放状态
    audioPlayer.addEventListener('timeupdate', function() {
      localStorage.setItem(storageKey, JSON.stringify({
        currentTime: audioPlayer.currentTime,
        isPlaying: !audioPlayer.paused
      }));
    });
  
  
    audioPlayer.addEventListener('pause', function() {
      localStorage.setItem(storageKey, JSON.stringify({
        currentTime: audioPlayer.currentTime,
        isPlaying: false
      }));
    });
  });