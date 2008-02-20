window.Animations = Array();



function chooseTable(database, table)
{	
	document.mochaDesktop.newWindow({
				id: 'table_s'+database+'_'+table,
				title: 'Details of table '+database+'.'+table,
				loadMethod: 'xhr',
				contentURL: './ajax/tableEditor/'+database+'/'+table,
				width: 320,
				height: 320,
				x: 20,
				y: 60
			});


}

function generateClass(database, table)
{
	document.mochaDesktop.newWindow({
				id: 'source_'+database+'_'+table,
				title: 'Generated class source for of table '+database+'.'+table,
				loadMethod: 'xhr',
				contentURL: './ajax/generateClass/'+database+'/'+table,
				width: 600,
				height: 400,
				x: 20,
				y: 60
			});
}

function generatePlugin(database, table)
{
	document.mochaDesktop.newWindow({
				id: 'plugin_'+database+'_'+table,
				title: 'Generated plugin source for of table '+database+'.'+table,
				loadMethod: 'xhr',
				contentURL: './ajax/generatePlugin/'+database+'/'+table,
				width: 600,
				height: 400,
				x: 20,
				y: 60
			});
}



function S (element_id) {
  var d=document;
  if(d.getElementById ) {
    var elem = d.getElementById(element_id);
    if(elem) {
      if(d.createRange) {
        var rng = d.createRange();
        if(rng.selectNodeContents) {
          rng.selectNodeContents(elem);
          if(window.getSelection) {
            var sel=window.getSelection();
            if(sel.removeAllRanges) sel.removeAllRanges();
            if(sel.addRange) sel.addRange(rng);
          }
        }
      } else if(d.body && d.body.createTextRange) {
        var rng = d.body.createTextRange();
        rng.moveToElementText(elem);
        rng.select();
      }
    }
  }
}