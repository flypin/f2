document.getElementById('page_bom').innerHTML=document.getElementById('page').innerHTML;

var a1 = document.getElementsByTagName('a');

window.onload = function()
{
}

$(document).ready(function(){
	$('#ajax_target').click(function() {
		var a1_html='';
		for(i=0;i<a1.length;i++)
		{
            if(a1[i].id && a1[i].id!="ajax_target" && a1[i].id!="max_id" ) a1_html +=a1[i].id+",";
		}
		$.get("http://tw.dog.yi.org/p/test1.php",
        { str: a1_html },
        function(data){
            alert("Data: " + data);
        });
	});
});


/*
var nextpage=a1[2].href;//Newest
var prevpage=a1[1].href;//Home
document.onkeydown = pageEvent; 
function pageEvent(evt){ 
    evt = evt ||window.event; 

    var key=evt.which||evt.keyCode;
    if(!event.shiftKey){
        if (key == 37) location = prevpage;
        if (key == 39) location = nextpage;
    }
}
*/

function keypress2() //textarea���볤�ȴ��� 
{ 
    var text1=document.getElementById("myarea").value; 
    var len;//��¼ʣ���ַ����ĳ��� 
    if(text1.length>=300)//textarea�ؼ�������maxlength���ԣ���ͨ��������ʾ�����ַ����� 
    { 
        document.getElementById("myarea").value=text1.substr(0,300); 
        len=0; 
    } 
    else 
    { 
        len=300-text1.length; 
    } 
    var show="�㻹��������"+len+"����"; 
    document.getElementById("pinglun").innerText=show; 
}