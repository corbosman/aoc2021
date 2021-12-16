#!/usr/bin/env python3

import sys
from math import inf
from heapq import heappush, heappop


def load_cave(path):
  with open(path, "r") as f:
    return [list(map(int, line.strip())) for line in f]


def get_dimensions(cave):
  w = len(cave[0])
  h = len(cave)
  return w,h


def extrapolate_cave(cave, times):
  w,h = get_dimensions(cave)
  extrapolated = [[0]*(w*5) for _ in range(h*5)]
  for y in range(h*5):
    for x in range(w*5):
      extrapolated[y][x] = 1 + (cave[y%h][x%w] + x//w + y//h - 1)%9 
  return extrapolated


def surrounding(node, w, h):
  x,y = node
  for offset_x, offset_y in [(-1,0), (0,-1), (1,0), (0,1)]:
    x2 = x + offset_x
    y2 = y + offset_y
    if x2>=0 and y2 >= 0 and x2 < w and y2 < h:
      yield (x2,y2)


def find_safest_route(cave):
  w,h = get_dimensions(cave)
  start_node = (0,0)
  end_node = (w-1,h-1)
  nodes_to_visit = [(0, start_node, [start_node])]
  minimum_risk = {}
  visited_nodes = set()

  while nodes_to_visit:
    risk, node, path = heappop(nodes_to_visit)

    if node == end_node:
      return (risk, path)

    for neighbor in surrounding(node, w, h):
      if neighbor in visited_nodes:
        continue
      
      to_risk = cave[neighbor[1]][neighbor[0]]
      known_risk_to_neighbour = minimum_risk.get(neighbor, inf)
      risk_via_this_node = risk + to_risk
      if risk_via_this_node < known_risk_to_neighbour:
        minimum_risk[neighbor] = risk_via_this_node
        queue_node = (risk_via_this_node, neighbor, [neighbor]+path)
        heappush(nodes_to_visit, queue_node)

    visited_nodes.add(node)


cave = load_cave(sys.argv[1]) 
cave = extrapolate_cave(cave, 5)
risk_level, _ = find_safest_route(cave)

print(risk_level)
