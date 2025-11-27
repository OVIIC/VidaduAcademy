<template>
  <div
    class="min-h-screen bg-secondary-900 flex items-center justify-center px-4 sm:px-6 lg:px-8"
  >
    <div class="max-w-md w-full space-y-8 text-center">
      <div>
        <h2 class="mt-6 text-3xl font-extrabold text-white">
          Prihlasovanie...
        </h2>
        <p class="mt-2 text-sm text-gray-400">
          Prosím čakajte, spracovávame vaše prihlásenie cez Google.
        </p>
      </div>
      <div class="flex justify-center mt-8">
        <div
          class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-500"
        ></div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import { useToast } from "vue-toastification";
import axios from "axios";

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
const toast = useToast();

onMounted(async () => {
  const { provider } = route.params;
  const { otp, error } = route.query;

  if (error) {
    toast.error("Prihlásenie zlyhalo: " + error);
    router.push({ name: "Login" });
    return;
  }

  if (!otp) {
    toast.error("Chýba autorizačný kód (OTP).");
    router.push({ name: "Login" });
    return;
  }

  try {
    // Exchange OTP for real token
    const response = await axios.post(
      `${import.meta.env.VITE_API_URL}/auth/social/exchange`,
      {
        otp,
      }
    );

    const { token } = response.data;

    if (!token) {
      throw new Error("Token not received from exchange");
    }

    // Store token
    authStore.token = token;
    authStore.isAuthenticated = true;

    // Save to local storage
    localStorage.setItem("token", token);

    // Set default axios header
    axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;

    // Fetch user details
    await authStore.fetchUser();

    toast.success("Prihlásenie úspešné!");

    // Redirect based on role
    if (authStore.isAdmin) {
      window.location.href = "/admin";
    } else {
      router.push({ name: "Dashboard" });
    }
  } catch (error) {
    console.error("Social login error:", error);
    toast.error("Prihlásenie zlyhalo. Skúste to prosím znova.");
    router.push({ name: "Login" });
  }
});
</script>
