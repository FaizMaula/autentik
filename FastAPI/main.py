from fastapi import FastAPI
from routers.certificate_router import router as certificate_router

app = FastAPI()

from fastapi.middleware.cors import CORSMiddleware

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)


app.include_router(certificate_router)

@app.get("/")
def root():
    return {"message": "API running"}
