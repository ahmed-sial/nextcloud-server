describe("TC-SI-03", () => {
  it("tests TC-SI-03", () => {
    cy.viewport(1089, 825);
      cy.visit("http://localhost:8080/login");
      cy.get("#user").clear();
    cy.get("#password").click();
    cy.get("#password").type("admin");
    cy.get("fieldset > button svg").click();
  });
});
