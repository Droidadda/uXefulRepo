jQuery('svg').find('path, rect, polyline, line, circle').each(function(e){
	startDrawingPath(jQuery(this).get(0));
});

function startDrawingPath(path){
	length = 0;
	timer = setInterval(function(){
		increaseLength(path);
	}, 1000/60);
}

function increaseLength(path){
	var pathLength = path.getTotalLength();
	length += 1;
	path.style.strokeDasharray = [length,pathLength].join(' ');
	if (length >= pathLength){
		clearInterval(timer);
	}
}
