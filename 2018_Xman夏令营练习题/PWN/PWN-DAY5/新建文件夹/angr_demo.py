#-*-:coding:utf-8

import angr 
import sys 
print "[*]start------------------------------------" 
p = angr.Project(sys.argv[1])  # 建立工程初始化二进制文件 
state = p.factory.entry_state() # 获取入口点处状态 
  
''' 
state.posix.files[0].read_from(1)表示从标准输入读取一个字节 
''' 
  
for _ in xrange(int(sys.argv[2])):  # 对输入进行简单约束（不为回车） 
    k = state.posix.files[0].read_from(1) 
    state.se.add(k!=10) 
  
k = state.posix.files[0].read_from(1) 
state.se.add(k==10)  # 回车为结束符 
  
state.posix.files[0].seek(0) 
state.posix.files[0].length = int(sys.argv[2])+1 # 约束输入长度（大于实际长度也可） 
  
print "[*]simgr start-------------------------------" 
  
sm = p.factory.simgr(state)   # 初始化进程模拟器 
sm.explore(find=lambda s:"correct!" in s.posix.dumps(1)) # 寻找运行过程中存在 “correct！”的路径，并丢弃其他路径 
print "[*]program excuted---------------------------" 
  
for pp in sm.found: 
    out = pp.posix.dumps(1)   # 表示程序的输出 
    print out 
    inp = pp.posix.files[0].all_bytes()  # 取输入的变量 
    print pp.solver.eval(inp,cast_to = str)  # 利用约束求解引擎求解输入 