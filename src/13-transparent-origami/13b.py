#!/usr/bin/env python3

import re


def load_instructions(path):
  dots = []
  instructions = []
  with open(path, "r") as f:
    for line in f:
      if groups := re.match(r'(\d+),(\d+)', line):
        dots.append((int(groups[1]), int(groups[2])))
      elif groups := re.match(r'fold along (x|y)=(\d+)', line):
        instructions.append((groups[1], int(groups[2])))
      elif line.strip() == "":
        pass
      else:
        print("Error in input line:", line)
  return dots, instructions


def make_paper(dots):
  width = max(x for x,_ in dots) + 1
  height = max(y for _,y in dots) + 1
  paper = [[" "]*width for _ in range(height)]
  for x,y in dots:
    paper[y][x] = "*"
  return paper


def divide(paper, axis, offset):
  if axis == "y":
    part1 = paper[:offset]
    part2 = paper[offset+1:]
  else:
    part1 = [line[:offset] for line in paper]
    part2 = [line[offset+1:] for line in paper]
  return part1, part2


def flip(paper, axis):
  if axis == "y":
    return paper[::-1] 
  else:
    return [line[::-1] for line in paper]


def overlay(part1, part2):
  w1,h1,w2,h2 = len(part1[0]), len(part1), len(part2[0]), len(part2)
  offset_x = w1 - w2
  offset_y = h1 - h2
  for y in range(h2):
    for x in range(w2):
      if part2[y][x] == "*":
        part1[y+offset_y][x+offset_x] = "*"
  return part1


def fold(paper, axis, offset):
  part1, part2 = divide(paper, axis, offset)
  part2 = flip(part2, axis)
  folded = overlay(part1, part2)
  return folded


def print_paper(paper):
  for line in paper:
    print("".join(line))


dots, instructions = load_instructions("input.txt")
paper = make_paper(dots)
for instruction in instructions:
  paper = fold(paper, *instruction)

print_paper(paper)