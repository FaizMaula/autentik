import "./bootstrap";

("use strict");

// Initialize Lucide icons when DOM ready
window.addEventListener("DOMContentLoaded", () => {
    if (window.lucide?.createIcons) {
        window.lucide.createIcons();
    }
});

// Password toggle visibility
(function () {
    const toggleButtons = document.querySelectorAll("[data-toggle-password]");
    if (!toggleButtons.length) return;

    toggleButtons.forEach((btn) => {
        btn.addEventListener("click", () => {
            const targetId = btn.getAttribute("data-toggle-password");
            const input = document.getElementById(targetId);
            const showIcon = btn.querySelector(".password-show");
            const hideIcon = btn.querySelector(".password-hide");

            if (input && showIcon && hideIcon) {
                if (input.type === "password") {
                    input.type = "text";
                    showIcon.classList.add("hidden");
                    hideIcon.classList.remove("hidden");
                } else {
                    input.type = "password";
                    showIcon.classList.remove("hidden");
                    hideIcon.classList.add("hidden");
                }
            }
        });
    });
})();

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

// User profile dropdown toggle (desktop & mobile)
(function () {
    const containers = document.querySelectorAll("[data-user]");
    if (!containers.length) return;

    function closeAll() {
        document
            .querySelectorAll("[data-user-menu]")
            .forEach((m) => m.classList.add("hidden"));
        document
            .querySelectorAll("[data-user-toggle]")
            .forEach((b) => b.setAttribute("aria-expanded", "false"));
    }

    containers.forEach((wrap) => {
        const btn = wrap.querySelector("[data-user-toggle]");
        const menu = wrap.querySelector("[data-user-menu]");
        if (!btn || !menu) return;
        btn.addEventListener("click", (e) => {
            e.stopPropagation();
            const isHidden = menu.classList.contains("hidden");
            closeAll();
            // Also close language menus
            document
                .querySelectorAll("[data-lang-menu]")
                .forEach((m) => m.classList.add("hidden"));
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

    btn.addEventListener("click", (e) => {
        e.preventDefault();
        e.stopPropagation();
        menu.classList.toggle("hidden");
    });

    // Close mobile menu when clicking outside
    document.addEventListener("click", (e) => {
        if (!btn.contains(e.target) && !menu.contains(e.target)) {
            menu.classList.add("hidden");
        }
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
    const fileDropZone = document.getElementById("fileDropZone");
    const dropOverlay = document.getElementById("dropOverlay");
    const fileDropper = document.getElementById("fileDropper");

    const allowedTypes = ["image/jpeg", "image/png", "application/pdf"];
    const maxSize = 2 * 1024 * 1024; // 2MB

    function clearPreview() {
        if (filePreviewWrap) filePreviewWrap.innerHTML = "";
        if (removeFileBtn) removeFileBtn.classList.add("hidden");
        if (fileDropper) fileDropper.classList.remove("hidden");
    }

    function showPreview(file) {
        clearPreview();
        if (!filePreviewWrap) return;

        // Hide icon and text after upload
        if (fileDropper) fileDropper.classList.add("hidden");
        if (removeFileBtn) removeFileBtn.classList.remove("hidden");

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

    function handleFile(file) {
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
    }

    if (fileInput) {
        fileInput.addEventListener("change", (e) => {
            const file = e.target.files?.[0];
            handleFile(file);
        });
    }

    // Drag & Drop handlers
    if (fileDropZone) {
        ["dragenter", "dragover", "dragleave", "drop"].forEach((eventName) => {
            fileDropZone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
            });
        });

        ["dragenter", "dragover"].forEach((eventName) => {
            fileDropZone.addEventListener(eventName, () => {
                if (dropOverlay) dropOverlay.classList.remove("hidden");
                fileDropZone.classList.add(
                    "border-[#B62A2D]",
                    "bg-[#B62A2D]/5"
                );
            });
        });

        ["dragleave", "drop"].forEach((eventName) => {
            fileDropZone.addEventListener(eventName, () => {
                if (dropOverlay) dropOverlay.classList.add("hidden");
                fileDropZone.classList.remove(
                    "border-[#B62A2D]",
                    "bg-[#B62A2D]/5"
                );
            });
        });

        fileDropZone.addEventListener("drop", (e) => {
            const files = e.dataTransfer?.files;
            if (files && files.length > 0) {
                const file = files[0];
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                fileInput.files = dataTransfer.files;
                handleFile(file);
            }
        });

        fileDropZone.addEventListener("click", (e) => {
            if (e.target.closest("button") || fileInput.files?.length > 0)
                return;
            fileInput.click();
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
            // Show processing modal
            const processingModal = document.getElementById("processingModal");
            if (processingModal) {
                processingModal.classList.remove("hidden");
                processingModal.classList.add("flex");
            }

            // Disable submit button to prevent double submission
            if (submitBtn) {
                submitBtn.setAttribute("disabled", "true");
                submitBtn.classList.add("opacity-50", "cursor-not-allowed");
            }

            // Form will submit normally to Laravel backend
            // The processing modal will stay visible during server processing
        });
    }
})();
