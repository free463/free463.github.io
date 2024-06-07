bg = new Array(8); 
bg[0] = './images/bg01.jpg width=1350 height=600' 
bg[1] = './images/bg02.jpg width=1350 height=600' 
bg[2] = './images/bg03.jpg width=1350 height=600' 
bg[3] = './images/bg04.jpg width=1350 height=600' 
bg[4] = './images/bg05.jpg width=1350 height=600' 
bg[5] = './images/bg06.jpg width=1350 height=600' 
bg[6] = './images/bg07.jpg width=1350 height=600' 
bg[7] = './images/bg08.jpg width=1350 height=600' 
index = Math.floor(Math.random() * bg.length);
document.write("<img src="+bg[index]+">");