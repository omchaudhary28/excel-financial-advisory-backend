const toggleApproval = async (id, approved) => {
  try {
    const formData = new FormData();
    formData.append("id", id);
    formData.append("approved", approved);

    await axios.post(
      `${API_BASE}/admin_feedback_toggle.php`,
      formData,
      {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      }
    );

    fetchRatings();
  } catch (err) {
    console.error(err);
    alert("Failed to update approval");
  }
};
