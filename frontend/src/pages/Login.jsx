import { useState } from "react";
import { useNavigate, Link } from "react-router-dom";
import { API_BASE_URL } from "../config";

function Login() {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState("");
  const navigate = useNavigate();

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError("");

    const response = await fetch(`${API_BASE_URL}/auth/login.php`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ email, password }),
    });
    const data = await response.json();

    if (!response.ok) {
      setError(data.error || "Login failed.");
      return;
    }

    localStorage.setItem("token", data.token);
    navigate("/dashboard");
  };

  return (
    <div className="min-h-screen flex items-center justify-center px-4">
      <div className="w-full max-w-md bg-paper/80 backdrop-blur-sm border border-hairline rounded-2xl p-10 shadow-sm">
        <h1 className="font-serif text-4xl mb-2">
          <span className="text-ink font-semibold">Grad</span>
          <span className="text-brass font-semibold">Path</span>
        </h1>
        <p className="text-muted text-base mb-8">
          Track your applications, start to finish.
        </p>

        <form onSubmit={handleSubmit} className="space-y-5">
          {error && (
            <p className="text-base text-red-700 bg-red-50 border border-red-200 px-4 py-2.5 rounded">
              {error}
            </p>
          )}

          <div>
            <label className="block text-base text-ink mb-1.5">Email</label>
            <input
              type="email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              className="w-full border border-hairline px-4 py-3 rounded text-base focus:outline-none focus:border-green"
            />
          </div>

          <div>
            <label className="block text-base text-ink mb-1.5">Password</label>
            <input
              type="password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              className="w-full border border-hairline px-4 py-3 rounded text-base focus:outline-none focus:border-green"
            />
          </div>

          <button
            type="submit"
            className="w-full bg-green text-white py-3 rounded text-base font-medium transition-all duration-200 hover:scale-105 hover:opacity-90"
          >
            Log in
          </button>
        </form>

        <p className="text-base text-muted mt-6">
          New here?{" "}
          <Link
            to="/register"
            className="text-green hover:underline transition-all duration-200 inline-block hover:scale-105"
          >
            Create an account
          </Link>
        </p>
      </div>
    </div>
  );
}

export default Login;
