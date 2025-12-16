from datetime import datetime

def generate_date_variations(date_str: str):
    dt = datetime.strptime(date_str, "%d/%m/%Y")
    return [
        dt.strftime("%d/%m/%Y"),
        dt.strftime("%d-%m-%Y"),
        dt.strftime("%d %b %Y"),
        dt.strftime("%B %d, %Y"),
        dt.strftime("%Y/%m/%d"),
        dt.strftime("%Y-%m-%d"),
        dt.strftime("%d %B %Y")
    ]

def normalize_date(date_str: str):
    dt = datetime.strptime(date_str, "%d/%m/%Y")
    return dt.strftime("%d %B %Y")
