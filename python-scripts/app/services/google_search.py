from googleapiclient.discovery import build

class GoogleSearchService:
    def __init__(self, api_key, cse_id):
        self.api_key = api_key
        self.cse_id = cse_id

    def search(self, query, num=5):
        service = build("customsearch", "v1", developerKey=self.api_key)
        res = service.cse().list(q=query, cx=self.cse_id, num=num, lr="lang_id").execute()
        items = res.get("items", [])
        if not items:
            return None
        top = items[0]
        return {"title": top["title"], "link": top["link"], "description": top.get("snippet", "-")}
