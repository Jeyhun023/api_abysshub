from copydetect import CopyDetector

detector = CopyDetector(test_dirs=["C:/Users/User/Desktop/www/abyss-hub/public/python/copydetect/tests"], display_t=1)
detector.add_file("C:/Users/User/Desktop/www/abyss-hub/public/python/copydetect/filter.py")
detector.run()
detector.generate_html_report()
