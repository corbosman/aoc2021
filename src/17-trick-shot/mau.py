#!/usr/bin/env python3

import re
import math
from itertools import groupby


def load_target_area(path):
  with open(path, "r") as f:
    match = re.match(r".*x=(\d+)\.\.(\d+).*y=([-\d]+)\.\.([-\d]+)", next(f))
    return dict(zip(
      ("x_min", "x_max", "y_min", "y_max"),
      map(int, match.groups())
    ))


def find_y_speed_options(target):
  y_min = target['y_min']
  y_max = target['y_max']

  # We can shoot downward using at most an initial speed that hits the deepest
  # part of the target (y_min) in one step. Any higher downward speed would
  # cause overshoot.
  min_speed = y_min

  # We can shoot upward using at most one less than the maximum speed
  # downward. When doing that, the speed in the step after reaching the
  # zero height point (initial height) will be equal to the min_speed.
  # Any higher upward speed would raise the speed after returning to the
  # initial height, causing an overshoot.
  max_speed = -1*y_min - 1

  for initial_speed in range(min_speed, max_speed+1):
    speed = initial_speed
    y,step = 0,0
    while y >= y_min:
      if y <= y_max:
        yield (initial_speed, step)
      step += 1
      y += speed 
      speed = speed-1


def find_x_speed_options(target, max_steps):
  x_min = target['x_min']
  x_max = target['x_max']

  # The minimum initial speed is the minimum speed at which the trajectory
  # can reach the target area before the horizontal motion is stopped.
  # For an extended explanation of how I got to this formula, see the
  # "strategy.txt" file.
  min_speed = math.ceil((math.sqrt(1+8*x_min) - 1) / 2)

  # We can shoot forward using at most an initial speed that hits the most
  # right part of the target (x_max) in one step. Any higher forward speed
  # would cause overshoot.
  max_speed = x_max

  for initial_speed in range(min_speed, max_speed+1):
    speed = initial_speed
    x = 0
    step = 0
    while step<=max_steps and x <= x_max:
      if x >= x_min:
        yield (initial_speed, step)
      step += 1
      x += speed 
      speed = speed-1 if speed > 0 else 0


def get_possible_initial_velocities(x_options, y_options):
  x_options = group_by_step(x_options)
  y_options = group_by_step(y_options)
  yield from intersect_options(x_options, y_options)


def group_by_step(options):
  def by_step(option):
    value, step = option
    return step
  sorted_options = sorted(options, key=by_step)
  grouped_options = groupby(sorted_options, key=by_step)
  return dict(
    (step, [o for o,_ in options])
    for step,options in grouped_options
  ) 


def intersect_options(x_options, y_options):
  for step, x_values in x_options.items():
    if step in y_options:
      y_values = y_options[step]
      for x in x_values:
        for y in y_values:
          yield (x,y)


target = load_target_area("inputs/input.txt")
y_options = list(find_y_speed_options(target))
max_steps = max(step for _,step in y_options)
x_options = find_x_speed_options(target, max_steps)
velocities = set(get_possible_initial_velocities(x_options, y_options))

print(len(velocities))