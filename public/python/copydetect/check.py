import sys
import os
sys.path.append("/home/abysshub_023/.local/lib/python3.8/site-packages")
from plagiarismdetect import CopyDetector

directory = os.path.normpath(os.path.join(__file__,'../../../../'))
detector = CopyDetector(test_dirs=[f"{directory}/storage/app/products/live"], display_t=0.1)
detector.add_file(f"{directory}/storage/app/products/temporary/{sys.argv[1]}", f"{sys.argv[2]}")

detector.run()
detector.generate_html_report()
