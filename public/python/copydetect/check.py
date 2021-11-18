import os
import sys

plagiarismdetect = import_module("/home/abysshub_023/.local/lib/python3.8/site-packages/plagiarismdetect")

sys.path.append("/home/abysshub_023/.local/lib/python3.8/site-packages")

directory = os.path.normpath(os.path.join(__file__,'../../../../'))
# {sys.argv[1]}
detector = plagiarismdetect.CopyDetector(test_dirs=[f"{directory}/storage/app/products/live"], display_t=0.1)
detector.add_file(f"{directory}/storage/app/products/temporary/0236260da63c1fa93f94e98035f8482a.py")
detector.run()
detector.generate_html_report()
