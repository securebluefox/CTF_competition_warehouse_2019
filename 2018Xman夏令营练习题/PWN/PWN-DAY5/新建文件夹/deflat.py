#coding: UTF-8

from barf.barf import BARF

import angr

import simuvex

import pyvex

import claripy

import struct

import sys


def get_retn_predispatcher(cfg):

    global main_dispatcher

    for block in cfg.basic_blocks:

        if len(block.branches) == 0 and block.direct_branch == None:

            retn = block.start_address

        elif block.direct_branch == main_dispatcher:

            pre_dispatcher = block.start_address

    return retn, pre_dispatcher


def get_relevant_nop_blocks(cfg):

    global pre_dispatcher, prologue, retn

    relevant_blocks = []

    nop_blocks = []

    for block in cfg.basic_blocks:

        if block.direct_branch == pre_dispatcher and len(block.instrs) != 1:

            relevant_blocks.append(block.start_address)

        elif block.start_address != prologue and block.start_address != retn:

            nop_blocks.append(block)

    return relevant_blocks, nop_blocks


def statement_inspect(state):

    global modify_value

    expressions = state.scratch.irsb.statements[state.inspect.statement].expressions

    if len(expressions) != 0 and isinstance(expressions[0], pyvex.expr.ITE):

        state.scratch.temps[expressions[0].cond.tmp] = modify_value

        state.inspect._breakpoints['statement'] = []


def symbolic_execution(start_addr, hook_addr=None, modify=None, inspect=False):

    global b, relevants, modify_value

    if hook_addr != None:

        b.hook(hook_addr, retn_procedure, length=5)

    if modify != None:

        modify_value = modify

    state = b.factory.blank_state(
        addr=start_addr, remove_options={simuvex.o.LAZY_SOLVES})

    if inspect:

        state.inspect.b('statement', when=simuvex.BP_BEFORE,
                        action=statement_inspect)

    p = b.factory.path(state)

    p.step()

    while p.successors[0].addr not in relevants:

        p = p.successors[0]

        p.step()

    return p.successors[0].addr


def retn_procedure(state):

    global b

    ip = state.se.any_int(state.regs.ip)

    b.unhook(ip)

    return


def fill_nop(data, start, end):

    global opcode

    for i in range(start, end):

        data[i] = opcode['nop']


def fill_jmp_offset(data, start, offset):

    jmp_offset = struct.pack('<i', offset)

    for i in range(4):

        data[start + i] = jmp_offset[i]


#python deflat.py filename entry


if __name__ == '__main__':

    if len(sys.argv) != 3:

        print 'Usage: python deflat.py filename function_address(hex)'

        exit(0)

    opcode = {'a': '\x87', 'ae': '\x83', 'b': '\x82', 'be': '\x86', 'c': '\x82', 'e': '\x84', 'z': '\x84', 'g': '\x8F',

              'ge': '\x8D', 'l': '\x8C', 'le': '\x8E', 'na': '\x86', 'nae': '\x82', 'nb': '\x83', 'nbe': '\x87', 'nc': '\x83',

              'ne': '\x85', 'ng': '\x8E', 'nge': '\x8C', 'nl': '\x8D', 'nle': '\x8F', 'no': '\x81', 'np': '\x8B', 'ns': '\x89',

              'nz': '\x85', 'o': '\x80', 'p': '\x8A', 'pe': '\x8A', 'po': '\x8B', 's': '\x88', 'nop': '\x90', 'jmp': '\xE9', 'j': '\x0F'}

    filename = sys.argv[1]  

    start = int(sys.argv[2], 16) 

    barf = BARF(filename)

    base_addr = barf.binary.entry_point >> 12 << 12

    print "snowtest="+str(barf.binary)

    print 'snowtest--base_addr%#x' % base_addr  

    b = angr.Project(filename, load_options={
                     'auto_load_libs': False, 'main_opts': {'custom_base_addr': 0}})

    cfg = barf.recover_cfg(ea_start=start)

    blocks = cfg.basic_blocks

#第一步:找出6大块,可以用静态分析得到

    #1.序言:序言为函数开始地址

    prologue = start

    #2.主分发器:序言的后继为主分发器(也就是序言指向的第一个块)

    main_dispatcher = cfg.find_basic_block(prologue).direct_branch

    #3.预处理器:后继为主分器的块位预处理器(也就是后面一个代码块是主分器的)

    #ida查找办法，对着主分发器按x键看交叉引用，调用主分发器的那个块

    #4.retn块:无后继的块为retn块(也就是没有任何下线分支的块)

    retn, pre_dispatcher = get_retn_predispatcher(cfg)

    #5.真实块:后继为预处理器的块为真实块

    #6.无用块:剩下的就是无用块

    relevant_blocks, nop_blocks = get_relevant_nop_blocks(cfg)  # 无用块(剩下的就是无用块)

    print '*******************relevant blocks************************'

    print 'func start addr prologue:%#x' % start  

    print 'main_dispatcher:%#x' % main_dispatcher  # 主分发器

    print 'pre_dispatcher:%#x' % pre_dispatcher  # 预处理器

    print 'func retn:%#x' % retn  # retn块

    print 'relevant_blocks:', [hex(addr) for addr in relevant_blocks]

    print '*******************symbolic execution*********************'

    relevants = relevant_blocks

    relevants.append(prologue)  # 加入序言

    relevants_without_retn = list(relevants)

    relevants.append(retn)

    flow = {}

    for parent in relevants:  

       # print "snow_parent=%x" % parent

        flow[parent] = []

    modify_value = None

    patch_instrs = {}

    for relevant in relevants_without_retn:

        print '-------------------dse %#x---------------------' % relevant

        block = cfg.find_basic_block(relevant)  # 找到上面块的范围

        has_branches = False

        hook_addr = None

        for ins in block.instrs:  # 遍历这些块打印出操作码

            #print "snowinstr="+ins.asm_instr.mnemonic

            if ins.asm_instr.mnemonic.startswith('cmov'):

                print "snow_has_branches=%s" % ins.asm_instr.mnemonic

                patch_instrs[relevant] = ins.asm_instr

                has_branches = True

            elif ins.asm_instr.mnemonic.startswith('call'):

                hook_addr = ins.address

                print "snow_hook_addr=%x" % hook_addr

        #难点一:使用symbolic_execution找出真实块和序言的调用关系，必须使用他的引擎运行或者动态运行

#第二步:找出真实块和序言和retn之间的调用关系,必须动态运行

        if has_branches:  

            #下面可能是修改标志寄存器达到往两个分支运行

            flow[relevant].append(symbolic_execution(
                relevant, hook_addr, claripy.BVV(1, 1), True))

            flow[relevant].append(symbolic_execution(
                relevant, hook_addr, claripy.BVV(0, 1), True))

        else:

            flow[relevant].append(symbolic_execution(relevant, hook_addr))

    print '************************flow******************************'


    for (k, v) in flow.items():

        print '%#x:' % k, [hex(child) for child in v]

    print '************************patch*****************************'

    flow.pop(retn)

    origin = open(filename, 'rb')

    origin_data = list(origin.read())

    origin.close()

    recovery = open(filename + '.recovered', 'wb')  # 输出文件路径

#第三步:nop掉无用块和主分发器和预处理器,可以静态分析得到

    for nop_block in nop_blocks:

         #无用块开始地址

         #下面是吧无用块填充0

        #print "snow_nop_block.start_address=%x" % nop_block.start_address

        fill_nop(origin_data, nop_block.start_address -
                 base_addr, nop_block.end_address - base_addr + 1)

#第四步:修复真实块序言部分的跳转指令，修复办法，

        #情况一:有一个childs，也就是一个后继的块，找到块的最后一条指令，将其抹掉后改成新的jmp，jmp到自己的childs

        #情况二:有多个childs的, 针对产生分支的真实块把CMOV指令改成相应的条件跳转指令跳向符合条件的分支，例如CMOVZ 改成JZ ，再在这条之后添加JMP 指令跳向另一分支

    for (parent, childs) in flow.items():

        if len(childs) == 1:  # 有一个childs的

           #print "snow_parent if=%x" % parent

            last_instr = cfg.find_basic_block(parent).instrs[-1].asm_instr

           #print "snow_last_instr addr=%x" % last_instr.address #找到最后一条指令地址，也就是jmp的地址

            file_offset = last_instr.address - base_addr  # 偏移地址

            origin_data[file_offset] = opcode['jmp']

            file_offset += 1

            fill_nop(origin_data, file_offset, file_offset +
                     last_instr.size - 1)  # 先填充为0

            fill_jmp_offset(origin_data, file_offset,
                            childs[0] - last_instr.address - 5)  # 然后填充jmp

        else:  # 有2个childs的

            #print "snow_parent else=%x" % parent

            instr = patch_instrs[parent]

            #print "snow_parent instr.address=%x" % instr.address 

            file_offset = instr.address - base_addr

            #nop掉cmov...指令到块结尾所有部分

            fill_nop(origin_data, file_offset, cfg.find_basic_block(
                parent).end_address - base_addr + 1)

            origin_data[file_offset] = opcode['j']

            origin_data[file_offset + 1] = opcode[instr.mnemonic[4:]]

            fill_jmp_offset(origin_data, file_offset + 2,
                            childs[0] - instr.address - 6)

            file_offset += 6

            origin_data[file_offset] = opcode['jmp']

            fill_jmp_offset(origin_data, file_offset + 1,
                            childs[1] - (instr.address + 6) - 5)

    recovery.write(''.join(origin_data))  # 把结果写回去

    recovery.close()

    print 'Successful! The recovered file: %s' % (filename + '.recovered')
