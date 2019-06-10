# -*- coding: utf-8 -*-
import argparse
import pytfm


if __name__ == '__main__':
    # parse the input arguments
    parser = argparse.ArgumentParser()
    parser.add_argument('input', type=str, help='input file to be transformed')
    parser.add_argument('output', type=str, help='output file ')
    args = parser.parse_args()

    # read the data and transform it
    with open(args.input, 'rb') as fin:
        with open(args.output, 'wb+') as fout:
            data = fin.read()
            fout.write(pytfm.transform(data))
