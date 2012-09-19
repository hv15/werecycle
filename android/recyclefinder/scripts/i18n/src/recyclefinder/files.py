'''
Module for dealing with resource files (but not their contents).

@author: Rodrigo Damazio
'''
import os.path
from glob import glob
import re

RECYCLEFINDER_RES_DIR = 'RecycleFinder/res'
ANDROID_MASTER_VALUES = 'values'
ANDROID_VALUES_MASK = 'values-*'


def GetRecycleFinderDir():
  '''
  Returns the directory in which the RecycleFinder directory is located.
  '''
  path = os.getcwd()
  while not os.path.isdir(os.path.join(path, RECYCLEFINDER_RES_DIR)):
    if path == '/':
      raise 'Not in RecycleFinder project'

    # Go up one level
    path = os.path.split(path)[0]

  return path


def GetAllLanguageFiles():
  '''
  Returns a mapping from all found languages to their respective directories.
  '''
  recyclefinder_path = GetRecycleFinderDir()
  res_dir = os.path.join(recyclefinder_path, RECYCLEFINDER_RES_DIR, ANDROID_VALUES_MASK)
  language_dirs = glob(res_dir)
  master_dir = os.path.join(recyclefinder_path, RECYCLEFINDER_RES_DIR, ANDROID_MASTER_VALUES)
  if len(language_dirs) == 0:
    raise 'No languages found!'
  if not os.path.isdir(master_dir):
    raise 'Couldn\'t find master file'

  language_tuples = [(re.findall(r'.*values-([A-Za-z-]+)', dir)[0],dir) for dir in language_dirs]
  language_tuples.append(('en', master_dir))
  return dict(language_tuples)
