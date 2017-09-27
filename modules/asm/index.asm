.386
.model flat,stdcall
includelib import32.lib
.const
PAGE_READWRITE = 4h
MEM_COMMIT = 1000h
MEM_RESERVE = 2000h
STD_INPUT_HANDLE = -10
STD_OUTPUT_HANDLE = -11

.data
hStdout dd ?
hStdin dd ?
hMem dd ?
header:
db 'Content-Type: text/html',13,10,13,10,0
start_html:
db 'Окружение CGI-программы выглядит так:<br>',13,10,0
for_stdin:
db 'STDIN программы содержит:<br>',13,10,0
end_html:

db '',13,10,0
nwritten dd ?
toscr db 10 dup (32)
db ' - Тип файла',0
.code
_start:

xor ebx,ebx
call GetStdHandle,STD_OUTPUT_HANDLE
mov hStdout,eax
call GetStdHandle,STD_INPUT_HANDLE
mov hStdin,eax

call write_stdout, offset header
call write_stdout, offset start_html

call VirtualAlloc,ebx,1000,MEM_COMMIT+MEM_RESERVE,PAGE_READWRITE
mov hMem,eax
mov edi,eax
call GetEnvironmentStringsA
mov esi,eax
next_symbol:
mov al,[esi]
or al,al
jz end_string
mov [edi],al
next_string:
cmpsb
jmp short next_symbol
end_string:
mov [edi],'>rb<'
add edi,3
cmp byte ptr [esi+1],0
jnz next_string
inc edi
stosb
call write_stdout, hMem
call write_stdout, offset for_stdin

call GetFileSize,[hStdin],ebx
mov edi,hMem
call ReadFile,[hStdin],edi, eax,offset nwritten, ebx
add edi,[nwritten]
mov byte ptr [edi],0
call write_stdout, hMem
call write_stdout, offset end_html
call VirtualFree,hMem
call ExitProcess,-1

write_stdout proc bufOffs:dword
call lstrlen,bufOffs
call WriteFile,[hStdout],bufOffs,eax,offset nwritten,0
ret
write_stdout endp
extrn GetEnvironmentStringsA:near
extrn GetStdHandle:near
extrn ReadFile:near
extrn WriteFile:near
extrn GetFileSize:near
extrn VirtualAlloc:near
extrn VirtualFree:near
extrn ExitProcess:near
extrn lstrlen:near
ends
end _start
