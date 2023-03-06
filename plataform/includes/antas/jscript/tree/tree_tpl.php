/*
	Feel free to use your custom images for the tree. Make sure they are all of the same size.
	User images collections are welcome, we'll publish them giving all regards.
*/


<?
if($a==1){?>
	var ruta = 'images/tree/';
<?}
else{?>
	var ruta = '../images/tree/';
<?}
?>

var TREE_TPL = {
	'target'  : 'frameset',	// name of the frame links will be opened in
							// other possible values are: _blank, _parent, _search, _self and _top

	'icon_e'  : ruta + 'empty.gif', // empty image
	'icon_l'  : ruta + 'line.gif',  // vertical line

	'icon_32' : ruta + 'base.gif',   // root leaf icon normal
	'icon_36' : ruta + 'base.gif',   // root leaf icon selected
	
	'icon_48' : ruta + 'base.gif',   // root icon normal
	'icon_52' : ruta + 'base.gif',   // root icon selected
	'icon_56' : ruta + 'base.gif',   // root icon opened
	'icon_60' : ruta + 'base.gif',   // root icon selected
	
	'icon_16' : ruta + 'folder.gif', // node icon normal
	'icon_20' : ruta + 'folderopen.gif', // node icon selected
	'icon_24' : ruta + 'folderopen.gif', // node icon opened
	'icon_28' : ruta + 'folderopen.gif', // node icon selected opened

	'icon_0'  : ruta + 'page.gif', // leaf icon normal
	'icon_4'  : ruta + 'page.gif', // leaf icon selected
	
	'icon_2'  : ruta + 'joinbottom.gif', // junction for leaf
	'icon_3'  : ruta + 'join.gif',       // junction for last leaf
	'icon_18' : ruta + 'plusbottom.gif', // junction for closed node
	'icon_19' : ruta + 'plus.gif',       // junctioin for last closed node
	'icon_26' : ruta + 'minusbottom.gif',// junction for opened node
	'icon_27' : ruta + 'minus.gif'       // junctioin for last opended node
};