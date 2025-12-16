from fastapi import FastAPI
from .routers import certificate_router

app = FastAPI(title="Certificate Verifier API")
app.include_router(certificate_router.router)
