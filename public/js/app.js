$(document).ready(function() {
    const title = document.getElementById('title-jhack');
    const text = document.getElementById('text-content-jhack');
    const colors = ['rgb(255, 95, 95)',
        'rgb(174, 255, 95)',
        'rgb(95, 219, 255)'];
    let count = 1;
    window.setInterval(changeTitleColor, 100);
    window.setInterval(changeTextColor, 100);

    function changeTitleColor() {
        title.style.color = colors[count++];
        if (count === 3)
            count = 0;
    }

    function changeTextColor() {
        text.style.color = colors[count++];
        if (count === 3)
            count = 0;
    }

});

