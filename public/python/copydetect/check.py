import sys
import os
sys.path.append("/home/abysshub_023/.local/lib/python3.8/site-packages")
from plagiarismdetect import CopyDetector
directory = os.path.normpath(os.path.join(__file__,'../../../../'))
detector = CopyDetector(test_dirs=[f"{directory}/storage/app/products/live"], display_t=0.1)
detector.add_file(f"{directory}/storage/app/products/temporary/0dc6eeb35b2e32f855a0d8b356ba584a.py", " ")

detector.run()
detector.generate_html_report()
