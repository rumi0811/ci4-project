
function printBalance(data,type,full)
{return data.toString().replace(/\B(?=(\d{3})+(?!\d))/g,".");}
function printBoolean(data,type,full)
{if(data==1)return"Yes";else if(data==0)return"No";else return"";}
function printUserDescription(data,type,full)
{arrData=data.split("|||");var resp="";if(arrData[0]!='')
resp+='<i class="fal fa-user"></i> '+arrData[0]+'<br />';if(arrData[1]!='')
resp+='<i class="fal fa-phone"></i> '+arrData[1]+'<br />';if(arrData[2]!='')
resp+='<i class="fal fa-home"></i> <small>'+arrData[2]+'</small>';return resp;}
function printUserDescription2(data,type,full)
{arrData=data.split("|||");var resp="";if(arrData[0]!='')
resp+='<i class="fal fa-user"></i> '+arrData[0]+'<br />';if(arrData[1]!='')
resp+='<h4> '+arrData[1]+'</h4>';if(arrData[2]!='')
resp+='<p> '+arrData[2]+'</p>';return resp;}
function printDate(data,type,full)
{if(data=='0000-00-00 00:00:00')return'';return data;}
function nl2br(str,is_xhtml){if(str=="")return"";var breakTag=(is_xhtml||typeof is_xhtml==='undefined')?'<br />':'<br>';return(str+'').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g,'$1'+breakTag+'$2');}
function nl2li(str){if(str=="")return"";var liTag='<li>';var liClosingTag='</li>';var result=(str+"\n").replace(/([^>\r\n]*)(\r\n|\n\r|\r|\n)/g,liTag+'$1'+liClosingTag);if(result!='')result='<ul>'+result+'</ul>';return result;}
function nl2oli(str){if(str=="")return"";var liTag='<li>';var liClosingTag='</li>';var result=(str+"\n").replace(/([^>\r\n]*)(\r\n|\n\r|\r|\n)/g,liTag+'$1'+liClosingTag);if(result!='')result='<ol>'+result+'</ol>';return result;};
