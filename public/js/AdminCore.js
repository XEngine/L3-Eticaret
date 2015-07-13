

function circle_progess() {
	
	var divElement = $('div'); //log all div elements
	
	$(".greenCircle").knob({
        'min':0,
        'max':100,
        'readOnly': true,
        'width': 120,
        'height': 120,
        'fgColor': '#b9e672',
        'dynamicDraw': true,
        'thickness': 0.2,
        'tickColorizeValues': true,
		'skin':'tron'
    })

    $(".orangeCircle").knob({
        'min':0,
        'max':100,
        'readOnly': true,
        'width': 120,
        'height': 120,
        'fgColor': '#FA5833',
        'dynamicDraw': true,
        'thickness': 0.2,
        'tickColorizeValues': true,
		'skin':'tron'
    })

	$(".lightOrangeCircle").knob({
        'min':0,
        'max':100,
        'readOnly': true,
        'width': 120,
        'height': 120,
        'fgColor': '#f4a70c',
        'dynamicDraw': true,
        'thickness': 0.2,
        'tickColorizeValues': true,
		'skin':'tron'
    })

    $(".blueCircle").knob({
        'min':0,
        'max':100,
        'readOnly': true,
        'width': 120,
        'height': 120,
        'fgColor': '#2FABE9',
        'dynamicDraw': true,
        'thickness': 0.2,
        'tickColorizeValues': true,
		'skin':'tron'
    })

	$(".yellowCircle").knob({
        'min':0,
        'max':100,
        'readOnly': true,
        'width': 120,
        'height': 120,
        'fgColor': '#e7e572',
        'dynamicDraw': true,
        'thickness': 0.2,
        'tickColorizeValues': true,
		'skin':'tron'
    })

	$(".pinkCircle").knob({
        'min':0,
        'max':100,
        'readOnly': true,
        'width': 120,
        'height': 120,
        'fgColor': '#e42b75',
        'dynamicDraw': true,
        'thickness': 0.2,
        'tickColorizeValues': true,
		'skin':'tron'
    })
	
	
}                
$(document).ready(function(){
	circle_progess();
});