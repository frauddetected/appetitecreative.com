<template>
    <figure>
        <div class="dot four"></div>
        <div class="dot three"></div>
        <div class="dot two"></div>
        <div class="dot one"></div>
    </figure>
</template>

<style>
figure { border-radius: 100%; width: 3em; height: 3em; position: relative; margin: auto; top: 0; bottom: 0; left: 0; right: 0; animation: colours 4s steps(1, start) infinite; }

.dot { position: absolute; width: 100%; height: 100%;  z-index:1; }
.dot:before, .dot:after { position: absolute; content: ""; display: block; perspective: 800px; transform-style: preserve-3d; } 

.dot.one:before { width: 50%; height: 100%; left: 0; background: #cd3820; border-radius: 2em 0 0 2em; } 
.dot.one:after { width: 50%; height: 100%; right: 0;  background: #cd3820; border-radius: 0 2em 2em 0;  }

.dot.two:before { width: 100%; height: 50%; top: 0; background: #fcb201; border-radius: 2em 2em 0 0; } 
.dot.two:after { width: 100%; height: 50%; bottom: 0;  background: #fcb201; border-radius: 0 0 2em 2em; }

.dot.three:before { width: 50%; height: 100%; left: 0; background: #019757; border-radius: 2em 0 0 2em; }
.dot.three:after { width: 50%; height: 100%; right: 0;  background: #019757; border-radius: 0 2em 2em 0;  } 

.dot.four:before { width: 100%; height: 50%; top: 0; background: #226ad9; border-radius: 2em 2em 0 0; }
.dot.four:after { width: 100%; height: 50%; bottom: 0;  background: #226ad9; border-radius: 0 0 2em 2em; } 

.dot.one:after { animation: dot1flip 4s linear infinite; transform-origin: left center; }
.dot.two:after { animation: dot2flip 4s linear infinite; transform-origin: top center; }
.dot.three:after { animation: dot3flip 4s linear infinite; transform-origin: left center; }
.dot.four:after { animation: dot4flip 4s linear infinite; transform-origin: top center; }

.dot.one { animation: dot1 4s linear infinite; opacity: 0; }
.dot.two { animation: dot2 4s linear infinite; opacity: 0; }
.dot.three { animation: dot3 4s linear infinite; opacity: 0; transform: rotate(180deg); }
.dot.four { animation: dot4 4s linear infinite; opacity: 0; transform: rotate(180deg); }

@keyframes colours {
  25% { background: #fcb201; }
  50% { background: #019757; }
  75% { background: #226ad9; }
  100% { background: #cd3820; }
}

@keyframes dot1 { 
  0% { opacity: 1; }
  25% { opacity: 1; }
  26% { opacity: 0; }
}
@keyframes dot2 { 
  24% { opacity: 0; }
  25% { opacity: 1; }
  50% { opacity: 1; }
  51% { opacity: 0; }
}
@keyframes dot3 { 
  49% { opacity: 0; }
  50% { opacity: 1; }
  75% { opacity: 1; }
  76% { opacity: 0; }
}
@keyframes dot4 { 
  74% { opacity: 0; }
  75% { opacity: 1; }
  99% { opacity: 1; }
  100% { opacity: 0; }
}

@keyframes dot1flip { 
  0% { transform: translateX( -0% ) rotateY( 0 ); background: #cd3820; }
  12.5% { background: #B02F1C; }
  12.6% { background: #FEC848; }
  25% { transform: translateX( -0% ) rotateY( 180deg ); background: #fcb201; }
}
@keyframes dot2flip {
  25% { transform: translateX( -0% ) rotateY( 0 ); background: #fcb201; }
  37.5% { background: #FEC848; }
  37.6% { background: #017A45; }
  50% { transform: translateY( -0% ) rotateX( -180deg ); background: #019757; }
}
@keyframes dot3flip { 
  50% { transform: translateX( -0% ) rotateY( 0 ); background: #019757; }
  62.5% { background: #01CB73; }
  62.6% { background: #1C57B0; }
  75% { transform: translateX( -0% ) rotateY( 180deg ); background: #226ad9; }
}
@keyframes dot4flip {
  75% { transform: translateX( -0% ) rotateY( 0 ); background: #226ad9; }
  87.5% { background: #1C57B0; }
  87.6% { background: #E3624F; }
  100% { transform: translateY( -0% ) rotateX( -180deg ); background: #cd3820; }
} 
</style>