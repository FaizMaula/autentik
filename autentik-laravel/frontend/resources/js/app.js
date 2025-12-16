import "./bootstrap";

("use strict");

// Initialize Lucide icons when DOM ready
window.addEventListener("DOMContentLoaded", () => {
    if (window.lucide?.createIcons) {
        window.lucide.createIcons();
    }
});

// Header scroll background toggle
(function () {
    const header = document.querySelector("header[data-app-header]");
    if (!header) return;
    const onScroll = () => {
        const scrolled = window.scrollY > 20;
        header.classList.toggle("shadow-lg", scrolled);
    };
    window.addEventListener("scroll", onScroll);
    onScroll();
})();

// Language dropdown toggle (desktop & mobile)
(function () {
    const containers = document.querySelectorAll("[data-lang]");
    if (!containers.length) return;

    function closeAll() {
        document
            .querySelectorAll("[data-lang-menu]")
            .forEach((m) => m.classList.add("hidden"));
        document
            .querySelectorAll("[data-lang-toggle]")
            .forEach((b) => b.setAttribute("aria-expanded", "false"));
    }

    containers.forEach((wrap) => {
        const btn = wrap.querySelector("[data-lang-toggle]");
        const menu = wrap.querySelector("[data-lang-menu]");
        if (!btn || !menu) return;
        btn.addEventListener("click", (e) => {
            e.stopPropagation();
            const isHidden = menu.classList.contains("hidden");
            closeAll();
            if (isHidden) {
                menu.classList.remove("hidden");
                btn.setAttribute("aria-expanded", "true");
            }
        });
    });

    document.addEventListener("click", () => closeAll());
    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") closeAll();
    });
})();

// Mobile menu toggle
(function () {
    const btn = document.getElementById("mobileMenuButton");
    const menu = document.getElementById("mobileMenu");
    if (!btn || !menu) return;
    btn.addEventListener("click", () => {
        menu.classList.toggle("hidden");
    });
})();

// Smooth scroll to #about-section when clicking elements with [data-scroll-about]
(function () {
    const scrollLinks = document.querySelectorAll("[data-scroll-about]");
    scrollLinks.forEach((link) => {
        link.addEventListener("click", (e) => {
            const isHome = window.location.pathname === "/";
            if (!isHome) {
                // Navigate to home with hash
                window.location.href = "/#about-section";
                return;
            }
            const target = document.getElementById("about-section");
            if (target) target.scrollIntoView({ behavior: "smooth" });
        });
    });
})();

// Smooth scroll to #process-section when clicking elements with [data-scroll-process]
(function () {
    const scrollLinks = document.querySelectorAll("[data-scroll-process]");
    scrollLinks.forEach((link) => {
        link.addEventListener("click", (e) => {
            const isHome = window.location.pathname === "/";
            if (!isHome) {
                // Navigate to home with hash
                window.location.href = "/#process-section";
                return;
            }
            const target = document.getElementById("process-section");
            if (target) target.scrollIntoView({ behavior: "smooth" });
        });
    });
})();

// Certificate form handlers (preview, validation, submit)
(function () {
    const form = document.getElementById("certForm");
    if (!form) return;

    const fileInput = document.getElementById("fileInput");
    const filePreviewWrap = document.getElementById("filePreviewContainer");
    const removeFileBtn = document.getElementById("removeFileButton");
    const submitBtn = document.getElementById("submitButton");

    const allowedTypes = ["image/jpeg", "image/png", "application/pdf"];
    const maxSize = 2 * 1024 * 1024; // 2MB

    function clearPreview() {
        if (filePreviewWrap) filePreviewWrap.innerHTML = "";
    }

    function showPreview(file) {
        clearPreview();
        if (!filePreviewWrap) return;
        if (file.type.startsWith("image/")) {
            const img = document.createElement("img");
            img.className = "max-h-64 mx-auto rounded-lg shadow-md";
            const reader = new FileReader();
            reader.onloadend = () => {
                img.src = reader.result;
            };
            reader.readAsDataURL(file);
            filePreviewWrap.appendChild(img);
        } else {
            const wrap = document.createElement("div");
            wrap.className =
                "flex items-center justify-center gap-3 p-4 bg-gray-50 rounded-lg";
            wrap.innerHTML = `<i data-lucide="file-text" class="text-red-500" style="width:48px;height:48px"></i>
				<div class="text-left">
					<p class="font-semibold text-gray-700">${file.name}</p>
					<p class="text-sm text-gray-500">${(file.size / (1024 * 1024)).toFixed(
                        2
                    )} MB</p>
				</div>`;
            filePreviewWrap.appendChild(wrap);
            if (window.lucide?.createIcons) window.lucide.createIcons();
        }
    }

    if (fileInput) {
        fileInput.addEventListener("change", (e) => {
            const file = e.target.files?.[0];
            if (!file) return;
            if (file.size > maxSize) {
                alert("File too large. Max 2 MB");
                fileInput.value = "";
                clearPreview();
                return;
            }
            if (!allowedTypes.includes(file.type)) {
                alert("Invalid file type. Allowed: JPG, PNG, PDF");
                fileInput.value = "";
                clearPreview();
                return;
            }
            showPreview(file);
        });
    }

    if (removeFileBtn) {
        removeFileBtn.addEventListener("click", () => {
            if (fileInput) fileInput.value = "";
            clearPreview();
        });
    }

    if (form) {
        form.addEventListener("submit", async (e) => {
            // Client-side required validations
            const requiredIds = [
                "name",
                "startDate",
                "endDate",
                "organizer",
                "eventName",
            ];
            for (const id of requiredIds) {
                const el = form.querySelector(`[name="${id}"]`);
                if (!el || !el.value) {
                    alert("Please fill in all required fields");
                    e.preventDefault();
                    return;
                }
            }
            const confirm = form.querySelector('[name="confirmData"]');
            if (!confirm?.checked) {
                alert("Please confirm your data");
                e.preventDefault();
                return;
            }
            if (!fileInput?.files?.length) {
                alert("Please choose a file");
                e.preventDefault();
                return;
            }
            // Simulate processing and redirect to results
            e.preventDefault();
            submitBtn?.setAttribute("disabled", "true");
            submitBtn?.classList.add("opacity-50", "cursor-not-allowed");
            const formData = new FormData(form);
            await fetch("http://127.0.0.1:8000/upload", {
                method: "POST",
                body: formData,
            });

        });
    }
})();
