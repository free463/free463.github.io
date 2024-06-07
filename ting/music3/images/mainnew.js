bg = new Array(8); 
bg[0] = 'https://code.aliyun.com/free163/publiclib/raw/08265923287e8067a337694679fca2c0344126f2/photopig/001.jpg width=1350 height=620' 
bg[1] = 'https://code.aliyun.com/free163/publiclib/raw/08265923287e8067a337694679fca2c0344126f2/photopig/002.jpg width=1350 height=620' 
bg[2] = 'https://code.aliyun.com/free163/publiclib/raw/08265923287e8067a337694679fca2c0344126f2/photopig/003.jpg width=1350 height=620' 
bg[3] = 'https://code.aliyun.com/free163/publiclib/raw/08265923287e8067a337694679fca2c0344126f2/photopig/004.jpg width=1350 height=620' 
bg[4] = 'https://code.aliyun.com/free163/publiclib/raw/08265923287e8067a337694679fca2c0344126f2/photopig/005.jpg width=1350 height=620' 
bg[5] = 'https://code.aliyun.com/free163/publiclib/raw/08265923287e8067a337694679fca2c0344126f2/photopig/006.jpg width=1350 height=620' 
bg[6] = 'https://code.aliyun.com/free163/publiclib/raw/08265923287e8067a337694679fca2c0344126f2/photopig/007.jpg width=1350 height=620' 
bg[7] = 'https://code.aliyun.com/free163/publiclib/raw/08265923287e8067a337694679fca2c0344126f2/photopig/008.jpg width=1350 height=620' 
index = Math.floor(Math.random() * bg.length);
document.write("<img src="+bg[index]+">");