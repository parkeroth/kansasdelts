
var items = [];

items[0] = {
    text: 'Some text 4 example',
    side: 'top',
    pos: '25%'
};

var opts = {
	name: 'kvak',
	items: items,
	imgFolder: '/img/tool_tip'
};

$('div.tips-form').kvkContBubbles(opts);

//

$('#show-tips').click(function () {
   $('div.tips-form').kvkCbShow();
});