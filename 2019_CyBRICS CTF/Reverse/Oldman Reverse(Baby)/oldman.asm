.MCALL  .TTYOUT,.EXIT
START:
    mov   #MSG r1 
    mov #0d r2
    mov #32d r3
loop:       
    mov   #MSG r1 
    add r2 r1
    movb    (r1) r0
    .TTYOUT
    sub #1d r3
    cmp #0 r3
    beq     DONE
    add #33d r2
    swab r2
    clrb r2
    swab r2    
    br      loop      
DONE: 
    .EXIT

MSG:
    .ascii "cp33AI9~p78f8h1UcspOtKMQbxSKdq~^0yANxbnN)d}k&6eUNr66UK7Hsk_uFSb5#9b&PjV5_8phe7C#CLc#<QSr0sb6{%NC8G|ra!YJyaG_~RfV3sw_&SW~}((_1>rh0dMzi><i6)wPgxiCzJJVd8CsGkT^p>_KXGxv1cIs1q(QwpnONOU9PtP35JJ5<hlsThB{uCs4knEJxGgzpI&u)1d{4<098KpXrLko{Tn{gY<|EjH_ez{z)j)_3t(|13Y}"
.end START