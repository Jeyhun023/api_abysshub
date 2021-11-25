import sys
import os
from plagiarismdetect import CopyDetector

directory = os.path.normpath(os.path.join(__file__,'../../../../'))
detector = CopyDetector(test_dirs=[f"{directory}/storage/app/products/live"], display_t=0.1)
detector.add_file(f"{directory}/storage/app/products/temporary/{sys.argv[1]}", f"{sys.argv[2]}")

detector.run()
detector.generate_html_report()
