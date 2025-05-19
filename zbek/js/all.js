// 更新后的JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const musicPlayer = document.querySelector('.music-player');
    const musicIcon = document.querySelector('.music-icon');
    const audio = document.getElementById('bgMusic');
    const volumeControl = document.querySelector('.volume-control');

    // 初始化本地存储状态
    const savedTime = localStorage.getItem('musicTime') || 0;
    const savedVolume = localStorage.getItem('musicVolume') || 1;
    const isPlaying = localStorage.getItem('musicPlaying') === 'true';

    audio.currentTime = savedTime;
    audio.volume = savedVolume;
    volumeControl.value = savedVolume;

    // 自动续播逻辑
    if(isPlaying) {
        audio.play().then(() => {
            musicPlayer.classList.add('playing');
        }).catch(() => {
            console.log('需要用户交互后才能自动播放');
        });
    }

    // 保存状态
    function saveState() {
        localStorage.setItem('musicTime', audio.currentTime);
        localStorage.setItem('musicVolume', audio.volume);
        localStorage.setItem('musicPlaying', !audio.paused);
    }

    // 事件监听
    musicIcon.addEventListener('click', function() {
        if (audio.paused) {
            audio.play();
            musicPlayer.classList.add('playing');
        } else {
            audio.pause();
            musicPlayer.classList.remove('playing');
        }
        saveState();
    });

    volumeControl.addEventListener('input', function(e) {
        audio.volume = e.target.value;
        saveState();
    });

    // 定期保存状态
    setInterval(saveState, 1000);

    // 页面关闭前保存
    window.addEventListener('beforeunload', saveState);
});